<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\Kelas;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruController extends Controller {
    /**
     * Dashboard Guru / Admin.
     */
    public function index(): View {
        $user = User::findOrFail(Auth::id());

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

        return view('guru.index', compact('user', 'school', 'aktifitas'));
    }

    /**
     * Profil Guru / Admin.
     */
    public function profil(): View {
        $user = User::findOrFail(auth()->id());

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
            ->limit(6)
            ->get();

        return view('guru.profil', compact('user', 'school', 'aktifitas'));
    }

    /**
     * Update profil Guru.
     *
     * Admin dapat mengubah Guru lain.
     * Guru biasa hanya dapat mengubah dirinya sendiri.
     */
    public function updateProfil(Request $request): Response {
        $id = (int) $request->input('id');

        $user = User::query()
            ->whereKey($id)
            ->whereIn('status', ['A', 'G'])
            ->firstOrFail();

        $actor = auth()->user();

        if ($actor->status !== 'A' && $actor->id !== $user->id) {
            abort(403);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'nama' => ['required', 'string', 'max:150'],
                'nis' => ['nullable', 'string', 'max:50'],
                'jk' => ['required', Rule::in(['L', 'P'])],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'password' => ['nullable', 'string', 'max:255'],
            ],
            [
                'nama.required' => 'Nama tidak boleh kosong.',
                'email.required' => 'Email tidak boleh kosong.',
                'email.email' => 'Email yang Anda masukan tidak valid.',
                'email.unique' => 'Email sudah terpakai, ganti dengan yang lain.',
                'jk.required' => 'Jenis kelamin wajib dipilih.',
                'jk.in' => 'Jenis kelamin tidak valid.',
            ],
        );

        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }

        $user->nama = trim((string) $request->input('nama'));

        $user->no_induk = trim((string) $request->input('nis', ''));

        $user->jk = $request->input('jk');

        $user->email = strtolower(trim((string) $request->input('email')));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah data guru atas nama ' . $user->nama,
        ]);

        return response('berhasil');
    }

    /**
     * Upload foto profil Guru.
     */
    public function uploadFotoUser(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }

        $user = User::query()
            ->whereKey((int) $request->input('id'))
            ->whereIn('status', ['A', 'G'])
            ->firstOrFail();

        $actor = auth()->user();

        if ($actor->status !== 'A' && $actor->id !== $user->id) {
            abort(403);
        }

        $image = $request->image('file')->orient()->scale(width: 550);

        $extension = $image->extension() ?: 'jpg';

        $filename = Str::uuid() . '.' . $extension;

        File::ensureDirectoryExists(public_path('img'));

        File::put(public_path('img/' . $filename), $image->toBytes());

        if (!empty($user->gambar)) {
            $oldImage = public_path('img/' . basename($user->gambar));

            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
        }

        $user->gambar = $filename;
        $user->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah foto profil',
        ]);

        return response('ubah');
    }

    /**
     * Update profil Sekolah.
     */
    public function updateProfilSekolah(Request $request): Response {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => ['required', 'integer', 'exists:schools,id'],
                'nama_sekolah' => ['required', 'string', 'max:150'],
                'alamat_sekolah' => ['nullable', 'string', 'max:255'],
                'motto_sekolah' => ['nullable', 'string', 'max:250'],
            ],
            [
                'nama_sekolah.required' => 'Nama sekolah tidak boleh kosong.',
            ],
        );

        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }

        $school = School::findOrFail((int) $request->input('id'));

        $school->nama = trim((string) $request->input('nama_sekolah'));

        $school->alamat = trim((string) $request->input('alamat_sekolah', ''));

        $school->motto = trim((string) $request->input('motto_sekolah', ''));

        $school->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah data profil sekolah',
        ]);

        return response('berhasil');
    }

    /**
     * Upload logo Sekolah.
     */
    public function uploadFotoSekolah(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'id_sekolah' => ['required', 'integer', 'exists:schools,id'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }

        $school = School::findOrFail((int) $request->input('id_sekolah'));

        $image = $request->image('file')->orient()->scale(width: 550);

        $extension = $image->extension() ?: 'jpg';

        $filename = Str::uuid() . '.' . $extension;

        File::ensureDirectoryExists(public_path('img'));

        File::put(public_path('img/' . $filename), $image->toBytes());

        if (!empty($school->logo)) {
            $oldImage = public_path('img/' . basename($school->logo));

            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
        }

        $school->logo = $filename;
        $school->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah logo sekolah',
        ]);

        return response('ubah');
    }

    /**
     * Master Data Kelas.
     */
    public function kelas(): View {
        $user = auth()->user();

        $school = School::first();

        $kelas = Kelas::query()
            ->withCount([
                'users as jumlah_siswa' => function ($query) {
                    $query->where('status', 'S');
                },
            ])
            ->orderBy('nama')
            ->paginate(15);

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
            ->limit(5)
            ->get();

        return view('guru.kelas', compact('user', 'school', 'kelas', 'aktifitas'));
    }

    /**
     * Ubah nama kelas.
     */
    public function ubahKelas(Request $request) {
        $validated = $request->validate(
            [
                'id' => ['required', 'integer', 'exists:kelas,id'],
                'nama' => ['required', 'string', 'max:255'],
            ],
            [
                'nama.required' => 'Nama kelas tidak boleh kosong.',
            ],
        );

        $kelas = Kelas::findOrFail($validated['id']);

        $kelas->nama = trim($validated['nama']);

        $kelas->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah kelas dengan nama ' . $kelas->nama,
        ]);

        return response($kelas->nama);
    }

    /**
     * Tambah kelas.
     */
    public function tambahKelas(Request $request) {
        $validated = $request->validate(
            [
                'nama' => ['required', 'string', 'max:255'],
            ],
            [
                'nama.required' => 'Nama tidak boleh kosong.',
            ],
        );

        $kelas = Kelas::create([
            'nama' => trim($validated['nama']),
        ]);

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Membuat kelas dengan nama ' . $kelas->nama,
        ]);

        return response('berhasil');
    }

    /**
     * Hapus kelas.
     *
     * Siswa yang berada di kelas tersebut
     * dikembalikan menjadi tanpa kelas.
     */
    public function hapusKelas(Request $request) {
        $validated = $request->validate([
            'id_kelas' => ['required', 'integer', 'exists:kelas,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $kelas = Kelas::findOrFail($validated['id_kelas']);

            User::query()
                ->where('status', 'S')
                ->where('id_kelas', (string) $kelas->id)
                ->update([
                    'id_kelas' => '',
                ]);

            Aktifitas::create([
                'id_user' => auth()->id(),
                'nama' => 'Menghapus kelas dengan nama ' . $kelas->nama,
            ]);

            $kelas->delete();
        });

        return response('berhasil');
    }

    /**
     * Detail kelas dan daftar siswa.
     */
    public function detailKelas(int $id): View {
        $user = auth()->user();

        $school = School::first();

        $kelassiswa = Kelas::findOrFail($id);

        $kelas = Kelas::query()->orderBy('nama')->get();

        $siswas = User::query()->where('status', 'S')->where('id_kelas', (string) $id)->orderBy('nama')->get();

        /*
         * Siswa kandidat untuk dipindahkan ke kelas ini.
         *
         * Siswa yang saat ini sudah berada di kelas
         * bersangkutan tidak ditampilkan.
         */
        $calonsiswas = User::query()
            ->where('status', 'S')
            ->where('id_kelas', '!=', (string) $id)
            ->orderBy('id_kelas')
            ->orderBy('nama')
            ->get();

        return view('guru.detailkelas', compact('user', 'school', 'kelas', 'kelassiswa', 'siswas', 'calonsiswas'));
    }

    /**
     * Cek kelas siswa sebelum dipindahkan.
     */
    public function cekKelasSiswa(Request $request) {
        $validated = $request->validate(
            [
                'siswa' => ['required', 'integer'],
            ],
            [
                'siswa.required' => 'Anda belum memilih siswa.',
            ],
        );

        $siswa = User::query()->whereKey($validated['siswa'])->where('status', 'S')->first();

        if (!$siswa) {
            return response('Data siswa tidak ditemukan.', 404);
        }

        if ($siswa->id_kelas === '' || $siswa->id_kelas === null) {
            return response('<b>' . e($siswa->nama) . '</b> belum memiliki kelas.');
        }

        $kelas = Kelas::find($siswa->id_kelas);

        if (!$kelas) {
            return response('<b>' . e($siswa->nama) . '</b> saat ini belum memiliki ' . 'kelas yang valid.');
        }

        return response(
            '<b>' .
                e($siswa->nama) .
                '</b> sekarang kelas <b>' .
                e($kelas->nama) .
                '</b>. Apakah akan dipindahkan? ' .
                'Klik tombol <b>Simpan</b> apabila ' .
                'benar akan dipindahkan.',
        );
    }

    /**
     * Pindahkan siswa ke kelas tertentu.
     */
    public function tambahSiswaKeKelas(Request $request) {
        $validated = $request->validate(
            [
                'siswa' => ['required', 'integer'],
                'id_kelas' => ['required', 'integer', 'exists:kelas,id'],
            ],
            [
                'siswa.required' => 'Anda belum memilih siswa ' . 'yang ingin dipindahkan kelas.',
            ],
        );

        $siswa = User::query()->whereKey($validated['siswa'])->where('status', 'S')->firstOrFail();

        $kelasBaru = Kelas::findOrFail($validated['id_kelas']);

        /*
         * Ambil kelas lama SEBELUM nilai
         * id_kelas pada siswa diubah.
         */
        $kelasLamaId = $siswa->id_kelas;

        $kelasLama = $kelasLamaId !== '' && $kelasLamaId !== null ? Kelas::find($kelasLamaId) : null;

        $namaKelasLama = $kelasLama?->nama ?? 'belum ada kelas';

        $siswa->id_kelas = (string) $kelasBaru->id;

        $siswa->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Memindahkan kelas siswa atas nama ' .
                $siswa->nama .
                ', NIS: ' .
                $siswa->no_induk .
                ' dari ' .
                $namaKelasLama .
                ' ke ' .
                $kelasBaru->nama,
        ]);

        return response('berhasil');
    }

    /**
     * Keluarkan siswa dari kelas.
     */
    public function hapusKelasSiswa(Request $request) {
        $validated = $request->validate([
            'id_siswa' => ['required', 'integer'],
        ]);

        $siswa = User::query()->whereKey($validated['id_siswa'])->where('status', 'S')->firstOrFail();

        $kelasLama = $siswa->id_kelas !== '' && $siswa->id_kelas !== null ? Kelas::find($siswa->id_kelas) : null;

        $namaKelasLama = $kelasLama?->nama ?? 'kelas tidak diketahui';

        $siswa->id_kelas = '';

        $siswa->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Mengeluarkan kelas siswa atas nama ' .
                $siswa->nama .
                ', NIS: ' .
                $siswa->no_induk .
                ' dari ' .
                $namaKelasLama .
                ' dan sekarang belum memiliki kelas',
        ]);

        return response('berhasil');
    }
}
