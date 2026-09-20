<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\School;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    /**
     * Dashboard Guru / Admin.
     */
    public function index(): View
    {
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
            ->limit(3)
            ->get();

        return view('guru.index', compact(
            'user',
            'school',
            'aktifitas'
        ));
    }

    public function profil(): View
    {
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

        return view('guru.profil', compact(
            'user',
            'school',
            'aktifitas'
        ));
    }

    public function updateProfil(Request $request): Response
    {
        $id = (int) $request->input('id');

        /*
        * Endpoint legacy ini juga dipakai halaman detail guru.
        * Karena itu ID tetap diterima, tetapi target dibatasi
        * hanya akun Admin / Guru.
        */
        $user = User::query()
            ->whereKey($id)
            ->whereIn('status', ['A', 'G'])
            ->firstOrFail();

        $validator = Validator::make(
            $request->all(),
            [
                'nama' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'nis' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'jk' => [
                    'required',
                    Rule::in(['L', 'P']),
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'nama.required' => 'Nama tidak boleh kosong.',

                'email.required' => 'Email tidak boleh kosong.',
                'email.email' => 'Email yang Anda masukan tidak valid.',
                'email.unique' => 'Email sudah terpakai, ganti dengan yang lain.',

                'jk.required' => 'Jenis kelamin wajib dipilih.',
                'jk.in' => 'Jenis kelamin tidak valid.',
            ]
        );

        if ($validator->fails()) {
            return response(
                $validator->errors()->first(),
                422
            );
        }

        $user->nama = $request->input('nama');
        $user->no_induk = $request->input('nis', '');
        $user->jk = $request->input('jk');
        $user->email = strtolower(trim($request->input('email')));

        if ($request->filled('password')) {
            $user->password = Hash::make(
                $request->input('password')
            );
        }

        $user->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah data guru atas nama '.$user->nama,
        ]);

        return response('berhasil');
    }

    public function uploadFotoUser(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => [
                    'required',
                    'integer',
                ],

                'file' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ]
        );

        if ($validator->fails()) {
            return response(
                $validator->errors()->first(),
                422
            );
        }

        $user = User::query()
            ->whereKey((int) $request->input('id'))
            ->whereIn('status', ['A', 'G'])
            ->firstOrFail();

        /*
        * Pertahankan konsep legacy:
        * gambar maksimum lebar 550px,
        * aspect ratio tetap.
        */
        $image = $request
            ->image('file')
            ->orient()
            ->scale(width: 550);

        $extension = $image->extension() ?: 'jpg';

        $filename = Str::uuid().'.'.$extension;

        File::ensureDirectoryExists(
            public_path('img')
        );

        File::put(
            public_path('img/'.$filename),
            $image->toBytes()
        );

        /*
        * Hapus foto sebelumnya.
        */
        if (! empty($user->gambar)) {
            $oldImage = public_path(
                'img/'.basename($user->gambar)
            );

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

    public function updateProfilSekolah(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => [
                    'required',
                    'integer',
                    'exists:schools,id',
                ],

                'nama_sekolah' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'alamat_sekolah' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'motto_sekolah' => [
                    'nullable',
                    'string',
                    'max:250',
                ],
            ],
            [
                'nama_sekolah.required' =>
                    'Nama sekolah tidak boleh kosong.',
            ]
        );

        if ($validator->fails()) {
            return response(
                $validator->errors()->first(),
                422
            );
        }

        $school = School::findOrFail(
            (int) $request->input('id')
        );

        $school->nama = $request->input('nama_sekolah');
        $school->alamat = $request->input('alamat_sekolah', '');
        $school->motto = $request->input('motto_sekolah', '');

        $school->save();

        Aktifitas::create([
            'id_user' => auth()->id(),
            'nama' => 'Merubah data profil sekolah',
        ]);

            return response('berhasil');
    }

    public function uploadFotoSekolah(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id_sekolah' => [
                    'required',
                    'integer',
                    'exists:schools,id',
                ],

                'file' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ]
        );

        if ($validator->fails()) {
            return response(
                $validator->errors()->first(),
                422
            );
        }

        $school = School::findOrFail(
            (int) $request->input('id_sekolah')
        );

        $image = $request
            ->image('file')
            ->orient()
            ->scale(width: 550);

        $extension = $image->extension() ?: 'jpg';

        $filename = Str::uuid().'.'.$extension;

        File::ensureDirectoryExists(
            public_path('img')
        );

        File::put(
            public_path('img/'.$filename),
            $image->toBytes()
        );

        if (! empty($school->logo)) {
            $oldImage = public_path(
                'img/'.basename($school->logo)
            );

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
}