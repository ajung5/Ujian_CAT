<?php

namespace App\Http\Controllers;

use App\Models\Aktifitas;
use App\Models\School;
use App\Models\User;
use Illuminate\View\View;

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
}