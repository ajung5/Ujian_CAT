<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SoalController extends Controller
{
    /**
     * Daftar Paket Soal.
     */
    public function index(): View
    {
        $user = User::findOrFail(
            auth()->id()
        );

        $query = Soal::query();

        /*
         * Legacy:
         * Admin melihat seluruh paket soal.
         * Guru hanya melihat paket soal miliknya.
         */
        if (auth()->user()->status === 'G') {
            $query->where(
                'id_user',
                auth()->id()
            );
        }

        $soals = $query
            ->orderByDesc('id')
            ->paginate(10);

        /*
         * Materi yang dapat dipilih untuk jenis Latihan.
         *
         * Materi harus milik user yang sedang login.
         * Materi yang sudah dipakai paket latihan
         * tidak ditawarkan kembali.
         */
        $materiTerpakai = Soal::query()
            ->whereNotNull('materi')
            ->where('materi', '!=', 0)
            ->pluck('materi');

        $materis = Materi::query()
            ->where(
                'id_user',
                auth()->id()
            )
            ->whereNotIn(
                'id',
                $materiTerpakai
            )
            ->orderBy('judul')
            ->get();

        return view(
            'guru.soal',
            compact(
                'user',
                'soals',
                'materis'
            )
        );
    }

    /**
     * Search Paket Soal.
     */
    public function search(
        Request $request
    ): View {
        $q = trim(
            (string) $request->input(
                'q',
                ''
            )
        );

        $query = Soal::query();

        if (auth()->user()->status === 'G') {
            $query->where(
                'id_user',
                auth()->id()
            );
        }

        if ($q !== '') {
            $query->where(
                'paket',
                'like',
                '%'.$q.'%'
            );
        }

        $soals = $query
            ->orderByDesc('id')
            ->paginate(15);

        return view(
            'guru.ajax.get_soal_guru',
            compact(
                'soals',
                'q'
            )
        );
    }

    /**
     * Membuat Paket Soal.
     *
     * Legacy:
     * POST /simpanformsoal
     */
    public function store(
        Request $request
    ): Response {
        $validated = $request->validate(
            [
                'jenis' => [
                    'required',
                    Rule::in(['1', '2']),
                ],

                'materi' => [
                    'nullable',
                    'integer',
                    'exists:materis,id',
                ],

                'paket' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'deskripsi' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'kkm' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:99999',
                ],

                'waktu' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ],
            [
                'jenis.required' =>
                    'Anda belum memilih jenis soal.',

                'jenis.in' =>
                    'Jenis soal tidak valid.',

                'materi.exists' =>
                    'Materi yang dipilih tidak ditemukan.',

                'paket.required' =>
                    'Anda belum menuliskan paket soal.',

                'deskripsi.required' =>
                    'Anda belum menuliskan deskripsi soal.',

                'kkm.required' =>
                    'Anda belum menuliskan KKM soal.',

                'kkm.integer' =>
                    'KKM harus berupa bilangan bulat.',

                'waktu.required' =>
                    'Anda belum menuliskan waktu soal.',

                'waktu.integer' =>
                    'Waktu harus ditulis dalam detik.',
            ]
        );

        /*
         * Jenis 2 = Latihan.
         *
         * Legacy mewajibkan materi.
         */
        if (
            $validated['jenis'] === '2' &&
            empty($validated['materi'])
        ) {
            return response(
                '<b>Error:</b> Anda belum memilih materi soal',
                422
            );
        }

        /*
         * Materi harus dimiliki user yang login.
         */
        if ($validated['jenis'] === '2') {
            $materiValid = Materi::query()
                ->whereKey(
                    $validated['materi']
                )
                ->where(
                    'id_user',
                    auth()->id()
                )
                ->exists();

            if (! $materiValid) {
                abort(403);
            }
        }

        $soal = new Soal();

        $soal->id_user =
            auth()->id();

        $soal->jenis =
            $validated['jenis'];

        /*
         * Ujian tidak mempunyai relasi Materi.
         */
        $soal->materi =
            $validated['jenis'] === '2'
                ? $validated['materi']
                : null;

        $soal->paket =
            trim(
                $validated['paket']
            );

        $soal->deskripsi =
            trim(
                $validated['deskripsi']
            );

        $soal->kkm =
            (string) $validated['kkm'];

        /*
         * Tetap disimpan dalam detik seperti legacy.
         */
        $soal->waktu =
            (string) $validated['waktu'];

        /*
         * Schema legacy memperbolehkan NULL.
         */
        $soal->tampil = null;

        $soal->save();

        return response('berhasil');
    }

    /**
     * Form edit Paket Soal.
     */
    public function edit(
        int $id
    ): View {
        $user = User::findOrFail(
            auth()->id()
        );

        $soal =
            $this->findAccessibleSoal(
                $id
            );

        return view(
            'guru.editsoal',
            compact(
                'user',
                'soal'
            )
        );
    }

    /**
     * Update Paket Soal.
     *
     * Sesuai legacy, edit hanya:
     * - paket
     * - deskripsi
     * - KKM
     * - waktu
     *
     * jenis dan materi tidak diubah.
     */
    public function update(
        Request $request
    ): Response {
        $validated = $request->validate(
            [
                'id_soal' => [
                    'required',
                    'integer',
                ],

                'paket' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'deskripsi' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'kkm' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:99999',
                ],

                'waktu' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ],
            [
                'paket.required' =>
                    'Anda belum menuliskan paket soal.',

                'deskripsi.required' =>
                    'Anda belum menuliskan deskripsi soal.',

                'kkm.required' =>
                    'Anda belum menuliskan KKM soal.',

                'waktu.required' =>
                    'Anda belum menuliskan waktu soal.',
            ]
        );

        $soal =
            $this->findAccessibleSoal(
                (int) $validated['id_soal']
            );

        $soal->paket =
            trim(
                $validated['paket']
            );

        $soal->deskripsi =
            trim(
                $validated['deskripsi']
            );

        $soal->kkm =
            (string) $validated['kkm'];

        $soal->waktu =
            (string) $validated['waktu'];

        $soal->save();

        return response('berhasil');
    }

    /**
     * Halaman konfirmasi delete.
     *
     * GET hanya menampilkan konfirmasi.
     */
    public function deleteConfirm(
        int $id
    ): View {
        $soal =
            $this->findAccessibleSoal(
                $id
            );

        return view(
            'guru.hapussoal',
            compact('soal')
        );
    }

    /**
     * Eksekusi delete Paket Soal.
     *
     * Dibuat POST agar GET tidak melakukan
     * perubahan data.
     */
    public function destroy(
        int $id
    ): RedirectResponse {
        $soal =
            $this->findAccessibleSoal(
                $id
            );

        /*
         * Untuk tahap ini kita mempertahankan
         * perilaku controller legacy:
         * hanya record paket soal yang dihapus.
         *
         * Cascade detail/distribusi/jawaban akan
         * ditentukan pada tahap integritas data.
         */
        $soal->delete();

        return redirect()
            ->route('guru.soal')
            ->with(
                'success',
                'Paket soal berhasil dihapus.'
            );
    }

    /**
     * Authorization Paket Soal.
     *
     * Admin = seluruh paket.
     * Guru = hanya miliknya.
     */
    private function findAccessibleSoal(
        int $id
    ): Soal {
        $query = Soal::query()
            ->whereKey($id);

        if (auth()->user()->status === 'G') {
            $query->where(
                'id_user',
                auth()->id()
            );
        }

        return $query->firstOrFail();
    }
}