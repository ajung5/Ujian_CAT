<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DataguruController extends Controller {
    /**
     * Daftar Guru.
     */
    public function index(): View {
        $user = Auth::user();

        $school = School::first();

        $users = User::query()->where('status', 'G')->orderBy('nama')->paginate(15);

        return view('guru.dataguru', compact('user', 'school', 'users'));
    }

    /**
     * Pencarian Guru via AJAX.
     */
    public function getUser(Request $request): View {
        $q = trim((string) $request->input('q'));

        $users = User::query()
            ->where('status', 'G')
            ->when($q !== '', fn($query) => $query->where('nama', 'like', '%' . $q . '%'))
            ->orderBy('nama')
            ->paginate(10);

        return view('guru.ajax.get_user', compact('users', 'q'));
    }

    /**
     * Tambah Guru.
     *
     * Hanya Administrator.
     */
    public function store(Request $request): Response {
        abort_unless(Auth::user()->status === 'A', 403);

        $validated = $request->validate(
            [
                'nama' => ['required', 'string', 'max:150'],
                'no_induk' => ['nullable', 'string', 'max:50'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'jk' => ['required', Rule::in(['L', 'P'])],
            ],
            [
                'nama.required' => 'Anda belum menuliskan nama guru.',
                'email.required' => 'Anda belum menuliskan email guru.',
                'email.email' => 'Email yang Anda masukan tidak valid.',
                'email.unique' => 'Email sudah terpakai, ganti dengan yang lain.',
                'jk.required' => 'Anda belum mengisi jenis kelamin guru.',
            ],
        );

        $guru = new User();

        /*
         * Kolom legacy NOT NULL.
         * Guru tidak terikat kelas.
         */
        $guru->id_kelas = '';

        $guru->nama = trim($validated['nama']);

        $guru->no_induk = trim((string) ($validated['no_induk'] ?? ''));

        $guru->jk = $validated['jk'];

        $guru->status = 'G';

        /*
         * Legacy menggunakan string kosong
         * untuk user yang belum memiliki foto.
         */
        $guru->gambar = '';

        $guru->email = strtolower(trim($validated['email']));

        /*
         * Password awal aplikasi lama.
         */
        $guru->password = Hash::make('123456');

        /*
         * Guru tidak menggunakan sekolah_asal,
         * tetapi kolom database legacy NOT NULL.
         */
        $guru->sekolah_asal = '';

        $guru->save();

        Aktifitas::create([
            'id_user' => Auth::id(),
            'nama' => 'Menambahkan guru atas nama ' . $guru->nama,
        ]);

        return response('berhasil');
    }

    /**
     * Detail Guru.
     */
    public function show(int $id): View {
        $user = User::query()->whereKey($id)->where('status', 'G')->firstOrFail();

        $school = School::first();

        $aktifitas = Aktifitas::query()
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
            ->limit(3)
            ->get();

        return view('guru.detailguru', compact('user', 'school', 'aktifitas'));
    }

    /**
     * Hapus Guru.
     *
     * Business logic legacy dipertahankan:
     * akun Guru dihapus langsung, tanpa cascade tambahan.
     */
    public function destroy(int $id) {
        abort_unless(Auth::user()->status === 'A', 403);

        $guru = User::query()->whereKey($id)->where('status', 'G')->firstOrFail();

        Aktifitas::create([
            'id_user' => Auth::id(),
            'nama' => 'Menghapus guru atas nama ' . $guru->nama,
        ]);

        $guru->delete();

        return redirect()->route('guru.data')->with('success', 'Data guru berhasil dihapus.');
    }
}
