<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\Countexamtime;
use App\Models\Jawab;
use App\Models\Kelas;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Throwable;

class DatasiswaController extends Controller
{
    /**
     * Daftar siswa dan calon siswa.
     */
    public function index(): View
    {
        $user = auth()->user();

        $school = School::first();

        $kelas = Kelas::query()
            ->orderBy('nama')
            ->get();

        $users = User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->whereIn(
                'users.status',
                ['S', 'C']
            )
            ->select([
                'users.id',
                'users.id_kelas',
                'users.no_induk',
                'users.nama',
                'users.email',
                'users.jk',
                'users.status',
                'users.sekolah_asal',
                'users.gambar',
                'kelas.nama as nama_kelas',
            ])
            ->orderBy('users.nama')
            ->paginate(10);

        $jumlahSiswa = User::query()
            ->where('status', 'S')
            ->count();

        return view('guru.siswa', compact(
            'user',
            'school',
            'kelas',
            'users',
            'jumlahSiswa'
        ));
    }

    /**
     * Cari siswa.
     */
    public function search(Request $request): View
    {
        $q = trim(
            (string) $request->input('q', '')
        );

        $users = User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->whereIn(
                'users.status',
                ['S', 'C']
            )
            ->when(
                $q !== '',
                function ($query) use ($q) {
                    $query->where(
                        'users.nama',
                        'like',
                        '%'.$q.'%'
                    );
                }
            )
            ->select([
                'users.id',
                'users.id_kelas',
                'users.no_induk',
                'users.nama',
                'users.email',
                'users.jk',
                'users.status',
                'users.sekolah_asal',
                'kelas.nama as nama_kelas',
            ])
            ->orderBy('users.nama')
            ->limit(15)
            ->get();

        return view(
            'guru.ajax.get_siswa',
            compact('users', 'q')
        );
    }

    /**
     * Tambah siswa manual.
     *
     * Password awal legacy: 123456.
     */
    public function store(Request $request): Response
    {
        $validated = $request->validate(
            [
                'nama' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'no_induk' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique(
                        'users',
                        'no_induk'
                    ),
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique(
                        'users',
                        'email'
                    ),
                ],

                'jk' => [
                    'required',
                    Rule::in(['L', 'P']),
                ],

                'id_kelas' => [
                    'required',
                    'integer',
                    'exists:kelas,id',
                ],
            ],
            [
                'nama.required' =>
                    'Anda belum menuliskan nama siswa.',

                'no_induk.required' =>
                    'Anda belum menuliskan NIS.',

                'no_induk.unique' =>
                    'NIS sudah terdaftar.',

                'email.required' =>
                    'Anda belum menuliskan email siswa.',

                'email.email' =>
                    'Email yang Anda masukan tidak valid.',

                'email.unique' =>
                    'Email sudah terdaftar.',

                'jk.required' =>
                    'Anda belum mengisi jenis kelamin siswa.',

                'id_kelas.required' =>
                    'Anda belum mengisi kelas siswa.',
            ]
        );

        $siswa = new User();

        $siswa->id_kelas = (string) $validated['id_kelas'];

        $siswa->nama = trim(
            $validated['nama']
        );

        $siswa->no_induk = trim(
            $validated['no_induk']
        );

        $siswa->jk = $validated['jk'];

        $siswa->status = 'S';

        /*
         * Legacy columns NOT NULL.
         */
        $siswa->gambar = '';

        $siswa->email = strtolower(
            trim($validated['email'])
        );

        $siswa->password =
            Hash::make('123456');

        $siswa->sekolah_asal = '';

        $siswa->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Menambahkan siswa atas nama '.
                $siswa->nama.
                ', NIS: '.
                $siswa->no_induk,
        ]);

        return response('berhasil');
    }

    /**
     * Detail siswa beserta histori ujian.
     */
    public function show(int $id): View
    {
        $user = auth()->user();

        $school = School::first();

        $siswa = User::query()
            ->leftJoin(
                'kelas',
                'users.id_kelas',
                '=',
                'kelas.id'
            )
            ->where(
                'users.id',
                $id
            )
            ->whereIn(
                'users.status',
                ['S', 'C']
            )
            ->select([
                'users.*',
                'kelas.nama as nama_kelas',
            ])
            ->firstOrFail();

        $aktifitas = Aktifitas::query()
            ->join(
                'users',
                'aktifitas.id_user',
                '=',
                'users.id'
            )
            ->select([
                'users.nama as nama_user',
                'users.gambar',
                'aktifitas.id',
                'aktifitas.id_user',
                'aktifitas.nama',
                'aktifitas.created_at',
                'aktifitas.updated_at',
            ])
            ->orderByDesc(
                'aktifitas.id'
            )
            ->limit(5)
            ->get();

       /*
        * Gunakan query builder untuk menjaga
        * kompatibilitas dengan Laravel 13.
        */
        $ujians = Jawab::query()
            ->join(
                'soals',
                'jawabs.id_soal',
                '=',
                'soals.id'
            )
            ->where(
                'jawabs.id_user',
                (string) $siswa->id
            )
            ->where(
                'jawabs.status',
                'Y'
            )
            ->selectRaw(
                '
                MIN(jawabs.id) as id,
                jawabs.id_soal,
                MAX(jawabs.updated_at) as updated_at,
                SUM(
                    CAST(
                        jawabs.score AS DECIMAL(10,2)
                    )
                ) as nilai,
                MAX(soals.paket) as paket,
                MAX(soals.kkm) as kkm,
                MAX(soals.waktu) as waktu
                '
            )
            ->groupBy(
                'jawabs.id_soal'
            )
            ->orderByDesc('id')
            ->paginate(25);

        $idSoal = collect(
            $ujians->items()
        )
            ->pluck('id_soal')
            ->filter()
            ->values();

        /*
         * Rincian jawaban untuk halaman
         * pagination yang sedang tampil.
         */
        $detailJawaban = collect();

        if ($idSoal->isNotEmpty()) {
            $detailJawaban = Jawab::query()
                ->join(
                    'detailsoals',
                    'jawabs.no_soal_id',
                    '=',
                    'detailsoals.id'
                )
                ->where(
                    'jawabs.id_user',
                    (string) $siswa->id
                )
                ->where(
                    'jawabs.status',
                    'Y'
                )
                ->whereIn(
                    'jawabs.id_soal',
                    $idSoal
                )
                ->select([
                    'jawabs.id',
                    'jawabs.id_soal',
                    'jawabs.no_soal_id',
                    'jawabs.pilihan',
                    'jawabs.score',
                    'detailsoals.soal',
                    'detailsoals.kunci',
                ])
                ->orderBy(
                    'jawabs.id'
                )
                ->get()
                ->groupBy('id_soal');
        }

        return view(
            'guru.detailkelassiswa',
            compact(
                'user',
                'school',
                'siswa',
                'ujians',
                'detailJawaban',
                'aktifitas'
            )
        );
    }

    /**
     * Update data siswa.
     *
     * Sesuai legacy:
     * email tidak diubah dari halaman detail.
     */
    public function update(Request $request): Response
    {
        $validated = $request->validate(
            [
                'id_siswa' => [
                    'required',
                    'integer',
                ],

                'nama' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'nis' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'jk' => [
                    'required',
                    Rule::in(['L', 'P']),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ]
        );

        $siswa = User::query()
            ->whereKey(
                $validated['id_siswa']
            )
            ->whereIn(
                'status',
                ['S', 'C']
            )
            ->firstOrFail();

        $duplicateNis = User::query()
            ->where(
                'no_induk',
                trim($validated['nis'])
            )
            ->where(
                'id',
                '!=',
                $siswa->id
            )
            ->exists();

        if ($duplicateNis) {
            return response(
                'NIS sudah digunakan.',
                422
            );
        }

        $siswa->nama = trim(
            $validated['nama']
        );

        $siswa->no_induk = trim(
            $validated['nis']
        );

        $siswa->jk =
            $validated['jk'];

        if (
            ! empty(
                $validated['password']
            )
        ) {
            $siswa->password =
                Hash::make(
                    $validated['password']
                );
        }

        $siswa->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Merubah data siswa atas nama '.
                $siswa->nama.
                ', NIS: '.
                $siswa->no_induk,
        ]);

        return response('berhasil');
    }

    /**
     * Upload foto siswa.
     *
     * Legacy maksimal 1 MB dan hanya
     * JPEG/JPG/PNG.
     */
    public function updatePhoto(Request $request): Response
    {
        $validated = $request->validate(
            [
                'id_siswa' => [
                    'required',
                    'integer',
                ],

                'file' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:1024',
                ],
            ],
            [
                'file.max' =>
                    'Ukuran foto maksimal 1 MB.',

                'file.mimes' =>
                    'Foto harus berupa JPG, JPEG, atau PNG.',
            ]
        );

        $siswa = User::query()
            ->whereKey(
                $validated['id_siswa']
            )
            ->whereIn(
                'status',
                ['S', 'C']
            )
            ->firstOrFail();

        $file = $request->file('file');

        $extension = strtolower(
            $file->extension()
        );

        $filename =
            Str::uuid().
            '.'.
            $extension;

        File::ensureDirectoryExists(
            public_path('img')
        );

        $file->move(
            public_path('img'),
            $filename
        );

        $oldImage = null;

        if (! empty($siswa->gambar)) {
            $oldImage = public_path(
                'img/'.
                basename($siswa->gambar)
            );
        }

        $siswa->gambar = $filename;
        $siswa->save();

        if (
            $oldImage &&
            File::exists($oldImage)
        ) {
            File::delete($oldImage);
        }

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Merubah foto siswa atas nama '.
                $siswa->nama,
        ]);

        return response(
            '<span id="success">'.
            'Foto berhasil di-upload.'.
            '</span>'
        );
    }

    /**
     * Hapus siswa.
     *
     * User, jawaban dan state timer ujian
     * ikut dihapus.
     */
    public function destroy(Request $request): Response
    {
        $validated = $request->validate([
            'id_siswa' => [
                'required',
                'integer',
            ],
        ]);

        $siswa = User::query()
            ->whereKey(
                $validated['id_siswa']
            )
            ->whereIn(
                'status',
                ['S', 'C']
            )
            ->firstOrFail();

        $nama = $siswa->nama;
        $gambar = $siswa->gambar;

        DB::transaction(
            function () use ($siswa) {
                Jawab::query()
                    ->where(
                        'id_user',
                        (string) $siswa->id
                    )
                    ->delete();

                Countexamtime::query()
                    ->where(
                        'id_user',
                        (string) $siswa->id
                    )
                    ->delete();

                $siswa->delete();
            }
        );

        if (! empty($gambar)) {
            $path = public_path(
                'img/'.
                basename($gambar)
            );

            if (File::exists($path)) {
                File::delete($path);
            }
        }

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Menghapus data siswa atas nama '.
                $nama,
        ]);

        return response('berhasil');
    }

    /**
     * Hapus seluruh calon siswa / peserta PSB.
     */
    public function destroyCandidates()
    {
        $candidates = User::query()
            ->where('status', 'C')
            ->get([
                'id',
                'gambar',
            ]);

        $ids = $candidates
            ->pluck('id')
            ->map(
                fn ($id) => (string) $id
            );

        DB::transaction(
            function () use ($ids) {
                if ($ids->isNotEmpty()) {
                    Jawab::query()
                        ->whereIn(
                            'id_user',
                            $ids
                        )
                        ->delete();

                    Countexamtime::query()
                        ->whereIn(
                            'id_user',
                            $ids
                        )
                        ->delete();
                }

                User::query()
                    ->where(
                        'status',
                        'C'
                    )
                    ->delete();
            }
        );

        foreach ($candidates as $candidate) {
            if (empty($candidate->gambar)) {
                continue;
            }

            $path = public_path(
                'img/'.
                basename(
                    $candidate->gambar
                )
            );

            if (File::exists($path)) {
                File::delete($path);
            }
        }

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' =>
                'Menghapus seluruh data calon siswa '.
                '(peserta PSB).',
        ]);

        return redirect()
            ->route('guru.siswa')
            ->with(
                'success',
                'Seluruh calon siswa berhasil dihapus.'
            );
    }

        /**
     * Import Siswa dari Excel. Format legacy data.xls:
     * A = ID Kelas
     * B = Nama
     * C = NIS
     * D = Jenis Kelamin
     * E = Email
     * F = Password
     */
    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'file' => [
                    'required',
                    'file',
                    'mimes:xls,xlsx',
                    'max:5120',
                ],
            ],
            [
                'file.required' =>
                    'File Excel siswa wajib dipilih.',

                'file.mimes' =>
                    'File harus berformat XLS atau XLSX.',

                'file.max' =>
                    'Ukuran file maksimal 5 MB.',
            ]
        );

        return $this->importUsersFromSpreadsheet(
            $request->file('file'),
            'S'
        );
    }

        /**
     * Import Calon Siswa dari Excel.
     *
     * Format legacy datacalon.xls:
     *
     * A = ID Kelas
     * B = Nama
     * C = ID Pendaftaran / NIS
     * D = Jenis Kelamin
     * E = Sekolah Asal
     * F = Email
     * G = Password
     */
    public function importCandidates(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'filecalon' => [
                    'required',
                    'file',
                    'mimes:xls,xlsx',
                    'max:5120',
                ],
            ],
            [
                'filecalon.required' =>
                    'File Excel calon siswa wajib dipilih.',

                'filecalon.mimes' =>
                    'File harus berformat XLS atau XLSX.',

                'filecalon.max' =>
                    'Ukuran file maksimal 5 MB.',
            ]
        );

        return $this->importUsersFromSpreadsheet(
            $request->file('filecalon'),
            'C'
        );
    }

        /**
     * Import user dari spreadsheet legacy.
     *
     * Status:
     * S = Siswa
     * C = Calon Siswa
     */
    private function importUsersFromSpreadsheet(UploadedFile $file, string $status): RedirectResponse
    {
        if (! in_array($status, ['S', 'C'], true)) {
            abort(400, 'Status import tidak valid.');
        }

        try {
            /*
            * Biarkan IOFactory mendeteksi XLS/XLSX.
            */
            $reader = IOFactory::createReaderForFile(
                $file->getRealPath()
            );

            /*
            * Kita hanya membutuhkan data.
            * Formatting, gambar dan chart tidak diperlukan.
            */
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load(
                $file->getRealPath()
            );

            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestDataRow();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'import' =>
                        'File Excel tidak dapat dibaca. '.
                        'Pastikan file menggunakan format '.
                        'XLS/XLSX yang valid.',
                ]);
        }

        /*
        * Cache referensi untuk menghindari query DB
        * berulang pada setiap baris.
        */
        $kelasValid = Kelas::query()
            ->pluck('id')
            ->mapWithKeys(
                fn ($id) => [
                    (string) $id => true,
                ]
            )
            ->all();

        $existingEmails = User::query()
            ->whereNotNull('email')
            ->pluck('email')
            ->map(
                fn ($email) =>
                    strtolower(
                        trim((string) $email)
                    )
            )
            ->filter()
            ->mapWithKeys(
                fn ($email) => [
                    $email => true,
                ]
            )
            ->all();

        $existingNis = User::query()
            ->whereNotNull('no_induk')
            ->where('no_induk', '!=', '')
            ->pluck('no_induk')
            ->map(
                fn ($nis) =>
                    trim((string) $nis)
            )
            ->filter()
            ->mapWithKeys(
                fn ($nis) => [
                    $nis => true,
                ]
            )
            ->all();

        $sukses = 0;
        $gagal = 0;
        $kosong = 0;

        $errors = [];

        /*
        * Baris pertama adalah header.
        *
        * Data dimulai dari baris 2,
        * sesuai Excel legacy.
        */
        for ($row = 2; $row <= $highestRow; $row++) {
            $idKelas = $this->spreadsheetCell(
                $sheet->getCell('A'.$row)->getValue()
            );

            $nama = $this->spreadsheetCell(
                $sheet->getCell('B'.$row)->getValue()
            );

            $nis = $this->spreadsheetCell(
                $sheet->getCell('C'.$row)->getValue()
            );

            $jk = strtoupper(
                $this->spreadsheetCell(
                    $sheet->getCell('D'.$row)->getValue()
                )
            );

            /*
            * Posisi kolom berbeda antara
            * siswa dan calon siswa.
            */
            if ($status === 'S') {
                $sekolahAsal = '';

                $email = strtolower(
                    $this->spreadsheetCell(
                        $sheet
                            ->getCell('E'.$row)
                            ->getValue()
                    )
                );

                $password = $this->spreadsheetCell(
                    $sheet
                        ->getCell('F'.$row)
                        ->getValue()
                );
            } else {
                $sekolahAsal =
                    $this->spreadsheetCell(
                        $sheet
                            ->getCell('E'.$row)
                            ->getValue()
                    );

                $email = strtolower(
                    $this->spreadsheetCell(
                        $sheet
                            ->getCell('F'.$row)
                            ->getValue()
                    )
                );

                $password =
                    $this->spreadsheetCell(
                        $sheet
                            ->getCell('G'.$row)
                            ->getValue()
                    );
            }

            /*
            * Lewati baris benar-benar kosong.
            */
            if (
                $idKelas === '' &&
                $nama === '' &&
                $nis === '' &&
                $jk === '' &&
                $email === '' &&
                $password === ''
            ) {
                $kosong++;

                continue;
            }

            $rowErrors = [];

            /*
            * ID kelas.
            */
            if ($idKelas === '') {
                $rowErrors[] =
                    'ID kelas kosong.';
            } elseif (
                ! isset($kelasValid[$idKelas])
            ) {
                $rowErrors[] =
                    'ID kelas '.$idKelas.
                    ' tidak ditemukan.';
            }

            /*
            * Nama.
            */
            if ($nama === '') {
                $rowErrors[] =
                    'Nama kosong.';
            } elseif (
                mb_strlen($nama) > 150
            ) {
                $rowErrors[] =
                    'Nama melebihi 150 karakter.';
            }

            /*
            * NIS / ID pendaftaran.
            */
            if ($nis === '') {
                $rowErrors[] =
                    $status === 'C'
                        ? 'ID Pendaftaran kosong.'
                        : 'NIS kosong.';
            } elseif (
                mb_strlen($nis) > 50
            ) {
                $rowErrors[] =
                    'NIS/ID Pendaftaran '.
                    'melebihi 50 karakter.';
            } elseif (
                isset($existingNis[$nis])
            ) {
                $rowErrors[] =
                    'NIS/ID Pendaftaran '.$nis.
                    ' sudah terdaftar.';
            }

            /*
            * Jenis kelamin.
            */
            if (
                ! in_array(
                    $jk,
                    ['L', 'P'],
                    true
                )
            ) {
                $rowErrors[] =
                    'Jenis kelamin harus L atau P.';
            }

            /*
            * Email.
            */
            if ($email === '') {
                $rowErrors[] =
                    'Email kosong.';
            } elseif (
                ! filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $rowErrors[] =
                    'Format email tidak valid.';
            } elseif (
                mb_strlen($email) > 255
            ) {
                $rowErrors[] =
                    'Email melebihi 255 karakter.';
            } elseif (
                isset($existingEmails[$email])
            ) {
                $rowErrors[] =
                    'Email '.$email.
                    ' sudah terdaftar.';
            }

            /*
            * Password Excel legacy memang berasal
            * dari spreadsheet.
            *
            * Pada versi baru password kosong ditolak
            * agar tidak menghasilkan akun dengan
            * password kosong.
            */
            if ($password === '') {
                $rowErrors[] =
                    'Password kosong.';
            }

            /*
            * Sekolah asal calon siswa.
            *
            * Tetap boleh kosong karena database
            * legacy menggunakan string kosong.
            */
            if (
                mb_strlen($sekolahAsal) > 255
            ) {
                $rowErrors[] =
                    'Sekolah asal melebihi 255 karakter.';
            }

            if ($rowErrors !== []) {
                $gagal++;

                $errors[] =
                    'Baris '.$row.': '.
                    implode(
                        ' ',
                        $rowErrors
                    );

                continue;
            }

            try {
                DB::transaction(
                    function () use (
                        $idKelas,
                        $nama,
                        $nis,
                        $jk,
                        $status,
                        $email,
                        $password,
                        $sekolahAsal
                    ) {
                        $siswa = new User();

                        $siswa->id_kelas =
                            $idKelas;

                        $siswa->nama =
                            $nama;

                        $siswa->no_induk =
                            $nis;

                        $siswa->jk =
                            $jk;

                        $siswa->status =
                            $status;

                        /*
                        * Kolom legacy NOT NULL.
                        */
                        $siswa->gambar = '';

                        $siswa->email =
                            $email;

                        $siswa->password =
                            Hash::make(
                                $password
                            );

                        $siswa->sekolah_asal =
                            $status === 'C'
                                ? $sekolahAsal
                                : '';

                        $siswa->save();
                    }
                );

                /*
                * Tambahkan ke cache agar duplicate
                * di baris berikutnya pada Excel
                * yang sama juga ditolak.
                */
                $existingEmails[$email] = true;

                $existingNis[$nis] = true;

                $sukses++;
            } catch (Throwable $exception) {
                report($exception);

                $gagal++;

                $errors[] =
                    'Baris '.$row.
                    ': gagal disimpan ke database.';
            }
        }

        /*
        * Bebaskan workbook dari memory.
        */
        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        $jenisImport =
            $status === 'S'
                ? 'siswa'
                : 'calon siswa';

        Aktifitas::create([
            'id_user' => auth()->id(),

            'nama' =>
                'Import Excel '.$jenisImport.
                ': '.$sukses.' berhasil, '.
                $gagal.' ditolak.',
        ]);

        return redirect()
            ->route('guru.siswa')
            ->with(
                'success',
                'Import Excel '.$jenisImport.
                ' selesai. Berhasil: '.
                $sukses.
                ', Ditolak: '.
                $gagal.'.'
            )
            ->with(
                'import_errors',
                $errors
            );
    }

    /**
     * Normalisasi nilai cell Excel menjadi string.
     */
    private function spreadsheetCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        /*
        * Jangan menjalankan formula Excel.
        *
        * getValue() dipakai, bukan
        * getCalculatedValue().
        */
        return trim(
            (string) $value
        );
    }
}