<?php

namespace App\Http\Controllers;

use App\Models\Distribusisoal;
use App\Models\Jawab;
use App\Models\School;
use App\Models\User;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Dashboard siswa.
     */
    public function index(): View
    {
        $user = User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->select(
                'users.*',
                'kelas.nama as nama_kelas'
            )
            ->where(
                'users.id',
                auth()->id()
            )
            ->firstOrFail();

        $school = School::first();

        return view(
            'siswa.index',
            compact(
                'user',
                'school'
            )
        );
    }


    /**
     * Daftar ujian yang tersedia
     * untuk kelas siswa.
     */
    public function exams(): View
    {
        $user = User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->select(
                'users.*',
                'kelas.nama as nama_kelas'
            )
            ->where(
                'users.id',
                auth()->id()
            )
            ->firstOrFail();

        $school = School::first();

        /*
         * Paket ujian yang sudah selesai.
         *
         * status Y pada jawabs berarti
         * jawaban sudah dikirim/final.
         */
        $completedExamIds = Jawab::query()
            ->where(
                'id_user',
                auth()->id()
            )
            ->where(
                'status',
                'Y'
            )
            ->pluck('id_soal')
            ->map(
                fn ($id) => (string) $id
            )
            ->unique()
            ->values()
            ->all();

        /*
         * Siswa tanpa kelas tidak memperoleh
         * paket ujian.
         */
        if (
            empty($user->id_kelas)
        ) {
            $distribusisoal =
                collect();

            return view(
                'siswa.soal',
                compact(
                    'user',
                    'school',
                    'distribusisoal'
                )
            );
        }

        /*
         * Pertahankan business process legacy:
         * paket muncul berdasarkan distribusi
         * ke kelas siswa.
         */
        $query = Distribusisoal::query()
            ->join(
                'soals',
                'distribusisoals.id_soal',
                '=',
                'soals.id'
            )
            ->select(
                'distribusisoals.id',
                'distribusisoals.id_soal',
                'distribusisoals.id_kelas',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis'
            )
            ->where(
                'distribusisoals.id_kelas',
                $user->id_kelas
            )
            ->where(
                'soals.jenis',
                '1'
            );

        /*
         * Paket yang sudah selesai
         * tidak ditampilkan lagi,
         * sesuai perilaku aplikasi lama.
         */
        if (
            $completedExamIds !== []
        ) {
            $query->whereNotIn(
                'soals.id',
                $completedExamIds
            );
        }

        $distribusisoal =
            $query
                ->orderByDesc(
                    'distribusisoals.id'
                )
                ->get();

        return view(
            'siswa.soal',
            compact(
                'user',
                'school',
                'distribusisoal'
            )
        );
    }
}