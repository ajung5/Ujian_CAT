<table class="
        table
        table-striped
        table-condensed
    " style="font-size:11pt;">
    <thead>
        <tr>
            <th class="center">
                #
            </th>

            <th>
                Paket Soal
            </th>

            <th>
                Jenis
            </th>

            <th class="center">
                Nilai
            </th>

            <th class="center">
                KKM
            </th>

            <th class="center">
                Status
            </th>

            <th>
                Tanggal
            </th>

            <th class="center">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody>
        @forelse ($results
        as $index => $result)
            @php

                $nilai = (float) $result->total_score;
                $kkm = (float) $result->kkm;
                $lulus = $nilai >= $kkm;
                $jenis = (int) $result->jenis_soal === 1 ? 'Ujian' : 'Latihan';
                $tanggal = !empty($result->completed_at) ? date('d-m-Y H:i', strtotime($result->completed_at)) : '-';
                $nomor = method_exists($results, 'firstItem') ? ($results->firstItem() ?? 1) + $index : $index + 1;
            @endphp

            <tr>
                <td class="center">
                    {{ $nomor }}
                </td>

                <td>
                    {{ $result->paket }}
                </td>

                <td>
                    @if ($jenis === 'Ujian')
                        <span style="color:#0441a3;">
                            Ujian
                        </span>
                    @else
                        <span style="color:#0493a3;">
                            Latihan
                        </span>
                    @endif
                </td>

                <td class="center">
                    <strong>
                        {{ rtrim(rtrim(number_format($nilai, 2, '.', ''), '0'), '.') }}
                    </strong>
                </td>

                <td class="center">
                    {{ $result->kkm }}
                </td>

                <td class="center">
                    @if ($lulus)
                        <span
                            style="
                            color:#009900;
                            font-weight:bold;
                        ">
                            Lulus
                        </span>
                    @else
                        <span
                            style="
                            color:#e60000;
                            font-weight:bold;
                        ">
                            Tidak Lulus
                        </span>
                    @endif
                </td>

                <td>
                    {{ $tanggal }}
                </td>

                <td class="center">
                    @if ((int) $result->jenis_soal === 2)
                        <a href="{{ route('siswa.results.detail', $result->id_soal) }}"
                            class="
                btn
                btn-sm
                btn-info
            ">
                            <i class="fa fa-eye"></i>

                            Review Jawaban
                        </a>
                    @else
                        <span class="text-muted">
                            Tidak Tersedia
                        </span>
                    @endif
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="8" class="alert alert-info">
                    Belum ada hasil ujian
                    untuk ditampilkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->lastPage() > 1)

    <ul class="pagination pagination-sm">
        <li class="{{ $results->onFirstPage() ? 'page-item disabled' : 'page-item' }}">
            <a class="page-link" href="{{ $results->previousPageUrl() ?? '#' }}">
                &laquo;
            </a>
        </li>

        @for ($page = 1; $page <= $results->lastPage(); $page++)
            <li class="{{ $results->currentPage() === $page ? 'page-item active' : 'page-item' }}">
                <a class="page-link" href="{{ $results->url($page) }}">
                    {{ $page }}
                </a>
            </li>
        @endfor

        <li class="{{ $results->hasMorePages() ? 'page-item' : 'page-item disabled' }}">
            <a class="page-link" href="{{ $results->nextPageUrl() ?? '#' }}">
                &raquo;
            </a>
        </li>
    </ul>

@endif
