@if ($materis->count())

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Status</th>

                <th class="text-center">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($materis as $materi)
                <tr id="baris{{ $materi->id }}">
                    <td style="width:30px;">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $materi->judul }}
                    </td>

                    <td style="width:100px;">
                        @if ($materi->status === 'Y')
                            <span class="label label-primary">
                                Tampil
                            </span>
                        @else
                            <span class="label label-danger">
                                Tidak tampil
                            </span>
                        @endif
                    </td>

                    <td class="text-center" style="width:130px;">
                        <a href="{{ route('guru.materi.edit', $materi->id) }}" class="btn btn-primary btn-xs">
                            <i
                                class="
                                fa
                                fa-pencil-square-o
                            "></i>
                        </a>

                        <button type="button"
                            class="
                            btn
                            btn-danger
                            btn-xs
                            btn-hapus-materi
                        "
                            data-id="{{ $materi->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                        <a href="{{ route('guru.materi.detail', $materi->id) }}" class="btn btn-success btn-xs">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-danger">
        <b>ERROR:</b>

        Kata kunci

        "<b>{{ $q }}</b>"

        tidak ditemukan.
    </div>

@endif
