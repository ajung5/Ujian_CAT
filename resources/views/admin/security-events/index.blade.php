@extends('layouts.guru_baru')

@section('title', 'Aktivitas Keamanan')

@section('content')
    @php
        $eventLabels = [
            'LOGIN_SUCCESS' => ['Login Berhasil', 'success'],
            'LOGIN_FAILED' => ['Login Gagal', 'danger'],
            'LOGOUT' => ['Logout', 'default'],
            'SESSION_REPLACED' => ['Session Diganti', 'warning'],
            'SESSION_INVALIDATED' => ['Session Ditolak', 'danger'],
            'SESSION_FORCE_REQUESTED' => ['Paksa Logout', 'warning'],
            'SESSION_LEGACY_CLAIMED' => ['Session Legacy Diadopsi', 'info'],
        ];

        $roleLabels = [
            'A' => 'Administrator',
            'G' => 'Guru',
            'S' => 'Siswa',
            'C' => 'Calon Siswa',
        ];
    @endphp

    <div class="col-md-12">
        <div class="pageheader">
            <h2>
                <i class="fa fa-shield"></i>
                Aktivitas Keamanan
            </h2>
        </div>
    </div>

    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Login Berhasil 24 Jam</h3>
            </div>
            <div class="panel-body">
                <h2>{{ $summary['login_success'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Login Gagal 24 Jam</h3>
            </div>
            <div class="panel-body">
                <h2>{{ $summary['login_failed'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Session Diganti 24 Jam</h3>
            </div>
            <div class="panel-body">
                <h2>{{ $summary['session_replaced'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">Session Ditolak 24 Jam</h3>
            </div>
            <div class="panel-body">
                <h2>{{ $summary['session_invalidated'] }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Session Siswa Terdaftar
                </h3>
            </div>

            <div class="panel-body">
                <p class="text-muted">
                    Daftar ini menunjukkan fingerprint session yang tercatat di server, bukan indikator bahwa browser sedang
                    online saat ini.
                </p>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Terakhir Login</th>
                                <th>IP Terakhir</th>
                                <th>Perangkat / Browser</th>
                                <th>Status</th>
                                <th style="width: 130px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentSessions as $student)
                                <tr>
                                    <td>{{ $student->nama }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        {{ $student->last_login_at?->format('d/m/Y H:i:s') ?? '-' }}
                                    </td>
                                    <td>{{ $student->last_login_ip ?? '-' }}</td>
                                    <td style="max-width: 320px; word-break: break-word">
                                        {{ $student->last_login_user_agent ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($student->student_session_revoked_at !== null)
                                            <span class="label label-danger">
                                                Direvoke
                                            </span>
                                        @elseif ($student->active_session_hash !== null)
                                            <span class="label label-success">
                                                Session Terdaftar
                                            </span>
                                        @else
                                            <span class="label label-default">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($student->student_session_revoked_at === null)
                                            <form method="POST"
                                                action="{{ route('admin.security.force-logout', $student->id) }}"
                                                onsubmit="return confirm('Paksa logout siswa ini? Session akan ditolak pada request berikutnya.');">
                                                @csrf

                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-sign-out"></i>
                                                    Paksa Logout
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">
                                                Menunggu login baru
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Belum ada session siswa yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Riwayat Aktivitas Keamanan
                </h3>
            </div>

            <div class="panel-body">
                <form method="GET" action="{{ route('admin.security.index') }}" class="form-inline"
                    style="margin-bottom: 20px">
                    <div class="form-group">
                        <label for="q">Cari</label>
                        <input id="q" type="text" name="q" class="form-control"
                            value="{{ $search }}" placeholder="Nama, email, IP">
                    </div>

                    <div class="form-group">
                        <label for="event">Event</label>
                        <select id="event" name="event" class="form-control">
                            <option value="">Semua Event</option>
                            @foreach ($eventOptions as $eventOption)
                                <option value="{{ $eventOption }}" @selected($eventFilter === $eventOption)>
                                    {{ $eventLabels[$eventOption][0] ?? $eventOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control">
                            <option value="">Semua Role</option>
                            @foreach ($roleLabels as $roleValue => $roleName)
                                <option value="{{ $roleValue }}" @selected($roleFilter === $roleValue)>
                                    {{ $roleName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i>
                        Filter
                    </button>

                    <a href="{{ route('admin.security.index') }}" class="btn btn-default">
                        Reset
                    </a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Event</th>
                                <th>Subjek</th>
                                <th>Role</th>
                                <th>IP</th>
                                <th>Actor</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $securityEvent)
                                @php
                                    $eventPresentation = $eventLabels[$securityEvent->event] ?? [
                                        $securityEvent->event,
                                        'default',
                                    ];
                                    $reason = $securityEvent->metadata['reason'] ?? null;
                                @endphp

                                <tr>
                                    <td style="white-space: nowrap">
                                        {{ $securityEvent->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="label label-{{ $eventPresentation[1] }}">
                                            {{ $eventPresentation[0] }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>
                                            {{ $securityEvent->user?->nama ?? '-' }}
                                        </strong>
                                        <br>
                                        <small>
                                            {{ $securityEvent->email ?? ($securityEvent->user?->email ?? '-') }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $roleLabels[$securityEvent->role] ?? ($securityEvent->role ?? '-') }}
                                    </td>
                                    <td>{{ $securityEvent->ip_address ?? '-' }}</td>
                                    <td>
                                        {{ $securityEvent->actor?->nama ?? 'System' }}
                                    </td>
                                    <td style="max-width: 360px">
                                        @if ($reason)
                                            <div>
                                                <strong>Reason:</strong>
                                                {{ $reason }}
                                            </div>
                                        @endif

                                        @if ($securityEvent->user_agent)
                                            <small class="text-muted" style="word-break: break-word">
                                                {{ $securityEvent->user_agent }}
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Belum ada aktivitas keamanan yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($events->hasPages())
                    <ul class="pager">
                        @if ($events->onFirstPage())
                            <li class="previous disabled">
                                <span>&larr; Sebelumnya</span>
                            </li>
                        @else
                            <li class="previous">
                                <a href="{{ $events->previousPageUrl() }}">
                                    &larr; Sebelumnya
                                </a>
                            </li>
                        @endif

                        @if ($events->hasMorePages())
                            <li class="next">
                                <a href="{{ $events->nextPageUrl() }}">
                                    Berikutnya &rarr;
                                </a>
                            </li>
                        @else
                            <li class="next disabled">
                                <span>Berikutnya &rarr;</span>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
