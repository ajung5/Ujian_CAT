<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\Materi;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MateriController extends Controller {
    /**
     * Daftar materi milik Guru/Admin yang login.
     */
    public function index(): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $aktifitas = $this->recentActivities();

        $materis = Materi::query()->where('id_user', auth()->id())->orderByDesc('id')->paginate(15);

        /*
         * Dipakai untuk menghubungkan upload gambar
         * dengan materi yang belum disimpan.
         */
        $sesiBaru = Str::random(32);

        return view('guru.materi', compact('user', 'school', 'aktifitas', 'materis', 'sesiBaru'));
    }

    /**
     * Halaman ubah materi.
     */
    public function edit(int $id): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $aktifitas = $this->recentActivities();

        /*
         * Materi hanya boleh diakses pemiliknya.
         *
         * Legacy hanya mencari berdasarkan ID sehingga
         * berpotensi IDOR.
         */
        $materi = Materi::query()->whereKey($id)->where('id_user', auth()->id())->firstOrFail();

        return view('guru.ubah_materi', compact('user', 'school', 'aktifitas', 'materi'));
    }

    /**
     * Simpan materi baru atau update materi berdasarkan sesi.
     *
     * Endpoint legacy:
     * POST /simpan-materi
     */
    public function save(Request $request): Response {
        $validated = $request->validate(
            [
                'sesi' => ['required', 'string', 'size:32'],
                'judul' => ['required', 'string', 'max:255'],
                'isi' => ['required', 'string'],
                'status' => ['required', Rule::in(['Y', 'N'])],
            ],
            [
                'sesi.required' => 'Sesi materi tidak ditemukan.',
                'judul.required' => 'Judul tidak boleh kosong.',
                'isi.required' => 'Isi materi tidak boleh kosong.',
                'status.required' => 'Status materi wajib dipilih.',
                'status.in' => 'Status materi tidak valid.',
            ],
        );

        $materi = Materi::query()->where('sesi', $validated['sesi'])->where('id_user', auth()->id())->first();

        $isNew = !$materi;

        if (!$materi) {
            $materi = new Materi();

            $materi->id_user = auth()->id();

            $materi->gambar = '';

            $materi->hits = 0;

            $materi->sesi = $validated['sesi'];
        }

        $materi->judul = trim($validated['judul']);

        /*
         * HTML dipertahankan karena field berasal
         * dari rich-text editor Summernote.
         */
        $materi->isi = $validated['isi'];

        $materi->status = $validated['status'];

        $materi->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => $isNew
                ? 'Menulis materi baru dengan judul ' . $materi->judul . '.'
                : 'Merubah materi dengan judul ' . $materi->judul . '.',
        ]);

        return response('ok');
    }

    /**
     * Detail materi.
     */
    public function show(int $id): View {
        $user = User::findOrFail(auth()->id());

        $school = School::first();

        $aktifitas = $this->recentActivities();

        $materi = Materi::query()->whereKey($id)->where('id_user', auth()->id())->firstOrFail();

        return view('guru.detail_materi', compact('user', 'school', 'aktifitas', 'materi'));
    }

    /**
     * Upload / replace gambar Materi.
     */
    public function uploadImage(Request $request): Response {
        $validated = $request->validate(
            [
                'sesi' => ['required', 'string', 'size:32'],
                'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ],
            [
                'file.required' => 'File gambar wajib dipilih.',
                'file.image' => 'File harus berupa gambar.',
                'file.mimes' => 'Gambar harus JPG, JPEG, PNG, atau WEBP.',
                'file.max' => 'Ukuran gambar maksimal 5 MB.',
            ],
        );

        /*
         * Laravel 13 Image API.
         *
         * Legacy resize 750px dan menjaga aspect ratio.
         * scale(width: 750) menghasilkan perilaku yang sama
         * dan tidak memperbesar gambar kecil.
         */
        $image = $request->image('file')->orient()->scale(width: 750);

        $extension = $image->extension() ?: 'jpg';

        $filename = Str::uuid() . '.' . $extension;

        $directory = public_path('img/materi');

        File::ensureDirectoryExists($directory);

        File::put($directory . '/' . $filename, $image->toBytes());

        $materi = Materi::query()->where('sesi', $validated['sesi'])->where('id_user', auth()->id())->first();

        /*
         * Bila user upload gambar sebelum tombol
         * Simpan ditekan, buat placeholder seperti
         * business process legacy.
         */
        if (!$materi) {
            $materi = new Materi();

            $materi->id_user = auth()->id();

            $materi->judul = '-';
            $materi->isi = '-';

            $materi->gambar = $filename;

            $materi->status = 'N';
            $materi->hits = 0;

            $materi->sesi = $validated['sesi'];

            $materi->save();

            return response('ok');
        }

        $oldImage = null;

        if (!empty($materi->gambar)) {
            $oldImage = public_path('img/materi/' . basename($materi->gambar));
        }

        $materi->gambar = $filename;

        $materi->save();

        /*
         * Hapus file lama setelah update database
         * berhasil.
         */
        if ($oldImage && File::exists($oldImage)) {
            File::delete($oldImage);
        }

        return response('ok');
    }

    /**
     * Hapus materi.
     */
    public function destroy(Request $request): Response {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $materi = Materi::query()->whereKey($validated['id'])->where('id_user', auth()->id())->firstOrFail();

        $judul = $materi->judul;

        $imagePath = null;

        if (!empty($materi->gambar)) {
            $imagePath = public_path('img/materi/' . basename($materi->gambar));
        }

        $materi->delete();

        if ($imagePath && File::exists($imagePath)) {
            File::delete($imagePath);
        }

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Menghapus materi miliknya ' . 'yang berjudul ' . $judul . '.',
        ]);

        return response('berhasil');
    }

    /**
     * Search materi via AJAX.
     */
    public function search(Request $request): View {
        $q = trim((string) $request->input('q', ''));

        $materis = Materi::query()
            ->where('id_user', auth()->id())
            ->when($q !== '', fn($query) => $query->where('judul', 'like', '%' . $q . '%'))
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return view('guru.ajax.get_materi', compact('materis', 'q'));
    }

    /**
     * Aktivitas sidebar.
     */
    private function recentActivities() {
        return Aktifitas::query()
            ->join('users', 'aktifitas.id_user', '=', 'users.id')
            ->select([
                'users.nama as nama_user',
                'users.gambar',
                'aktifitas.id',
                'aktifitas.id_user',
                'aktifitas.nama',
                'aktifitas.created_at',
                'aktifitas.updated_at',
            ])
            ->orderByDesc('aktifitas.id')
            ->limit(5)
            ->get();
    }
}
