<?php

namespace App\Http\Controllers;

use App\Models\Jawab;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HasilController extends Controller
{
    /**
     * Daftar paket yang sudah memiliki hasil.
     *
     * Admin:
     * - dapat melihat seluruh paket.
     *
     * Guru:
     * - hanya paket miliknya sendiri.
     */
    public function index(): View
    {
        $user = User::findOrFail(
            auth()->id()
        );

        $school = School::first();

        $jawabs = $this->resultsQuery()
            ->paginate(10);

        return view(
            'guru.hasil',
            compact(
                'user',
                'school',
                'jawabs'
            )
        );
    }

    /**
     * Search laporan melalui AJAX.
     */
    public function search(Request $request): View
    {
        $validated = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:150',
            ],
        ]);

        $q = trim(
            (string) ($validated['q'] ?? '')
        );

        $jawabs = $this->resultsQuery($q)
            ->limit(50)
            ->get();

        return view(
            'guru.ajax.get_hasil_guru',
            compact('jawabs')
        );
    }

    /**
     * Query dasar laporan.
     *
     * Hanya jawaban final status=Y yang
     * dianggap sebagai hasil pengerjaan.
     */
    private function resultsQuery(
        ?string $search = null
    ): Builder {
        $query = Jawab::query()
            ->join(
                'soals',
                'jawabs.id_soal',
                '=',
                'soals.id'
            )
            ->select(
                'soals.id as id_soal',
                'soals.id_user',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
                'soals.created_at',

                \DB::raw(
                    'COUNT(DISTINCT jawabs.id_user) as jumlah_peserta'
                ),

                \DB::raw(
                    'MAX(jawabs.updated_at) as terakhir_dikerjakan'
                )
            )
            ->where(
                'jawabs.status',
                'Y'
            );

        /*
         * Guru hanya boleh melihat
         * hasil paket yang dibuat olehnya.
         *
         * Admin dapat melihat semua.
         */
        if (
            auth()->user()->status === 'G'
        ) {
            $query->where(
                'soals.id_user',
                auth()->id()
            );
        }

        if (
            $search !== null &&
            $search !== ''
        ) {
            $query->where(
                'soals.paket',
                'like',
                '%'.$search.'%'
            );
        }

        return $query
            ->groupBy(
                'soals.id',
                'soals.id_user',
                'soals.paket',
                'soals.deskripsi',
                'soals.kkm',
                'soals.waktu',
                'soals.jenis',
                'soals.created_at'
            )
            ->orderByDesc(
                'terakhir_dikerjakan'
            );
    }
}