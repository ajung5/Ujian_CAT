@extends('layouts.siswa_baru')

@section('title', 'Latihan')

@section('breadcrumb')

    <li>
        <a href="{{ route('siswa.index') }}">
            Home
        </a>
    </li>

    <li class="active">
        Latihan
    </li>

@endsection

@section('content')

    <div class="col-md-12">
        @if ($materis->count())
            <div class="card-columns">
                @foreach ($materis as $materi)
                    <div class="card">
                        <div
                            class="
                            card-header
                            bg-white
                            center
                        ">
                            <h4 class="card-title">
                                <a
                                    href="{{ route('siswa.latihan.detail', [
                                        'id' => $materi->id,
                                        'judul' => \Illuminate\Support\Str::slug($materi->judul),
                                    ]) }}">
                                    {{ $materi->judul }}
                                </a>
                            </h4>
                        </div>

                        @if (!empty($materi->gambar))
                            <a
                                href="{{ route('siswa.latihan.detail', [
                                    'id' => $materi->id,
                                    'judul' => \Illuminate\Support\Str::slug($materi->judul),
                                ]) }}">
                                <div
                                    style="
                                    overflow:hidden;
                                    height:150px;
                                ">
                                    <img src="{{ asset('img/materi/' . basename($materi->gambar)) }}"
                                        alt="{{ $materi->judul }}"
                                        style="
                                        width:100%;
                                        min-height:150px;
                                        object-fit:cover;
                                    ">
                                </div>
                            </a>
                        @endif

                        <div class="card-block">
                            <p class="m-b-0">
                                {{ \Illuminate\Support\Str::limit(trim(strip_tags($materi->isi)), 180) }}
                            </p>

                            @if ($materi->user)
                                <p
                                    style="
                                    margin-top:10px;
                                    color:#999;
                                    font-size:12px;
                                ">
                                    Oleh:
                                    {{ $materi->user->nama }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="clearfix"></div>

            @if ($materis->lastPage() > 1)

                <ul class="pagination">
                    @for ($page = 1; $page <= $materis->lastPage(); $page++)
                        <li class="{{ $materis->currentPage() === $page ? 'active' : '' }}">
                            <a href="{{ $materis->url($page) }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endfor
                </ul>

            @endif
        @else
            <div class="alert alert-info">
                <i class="
                    fa
                    fa-info-circle
                "></i>

                Belum ada materi latihan
                yang tersedia.
            </div>

        @endif
    </div>

@endsection
