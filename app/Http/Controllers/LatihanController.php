<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\School;
use App\Models\Soal;
use App\Models\User;
use Illuminate\View\View;

class LatihanController extends Controller {
    /**
     * Daftar materi aktif untuk siswa.
     *
     * Business process legacy:
     * - semua materi status Y dapat dibaca siswa
     * - tidak bergantung kelas
     * - pagination 4 materi
     */
    public function index(): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $materis = Materi::query()
            ->with(['user:id,nama,gambar,status'])
            ->where('status', 'Y')
            ->orderByDesc('id')
            ->paginate(4);

        return view('siswa.latihan.index', compact('user', 'school', 'materis'));
    }

    /**
     * Detail materi aktif.
     *
     * Endpoint legacy:
     * /latihan/read/{id}/{judul}
     */
    public function detail(int $id, string $judul): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        /*
         * Materi status N tidak boleh
         * dibaca langsung melalui URL.
         */
        $materi = Materi::query()
            ->with(['user:id,nama,gambar,status'])
            ->whereKey($id)
            ->where('status', 'Y')
            ->firstOrFail();

        /*
         * Paket latihan tidak menggunakan
         * distribusi kelas.
         *
         * Relasinya:
         * soals.jenis = 2
         * soals.materi = materis.id
         */
        $soals = Soal::query()->where('jenis', '2')->where('materi', $materi->id)->orderByDesc('id')->get();

        return view('siswa.latihan.detail', compact('user', 'school', 'materi', 'soals'));
    }
}
