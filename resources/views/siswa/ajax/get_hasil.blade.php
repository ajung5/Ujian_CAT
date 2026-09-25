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
        @forelse ($results as $index => $result)
            @php
                $nilai = (float) $result->total_score;
                $kkm = (float) $result->kkm;
                $lulus = $nilai >= $kkm;
                $isExam = (int) $result->jenis_soal === 1;
                $jenis = $isExam ? 'Ujian' : 'Latihan';

                $tanggal = !empty($result->completed_at) ? date('d-m-Y H:i', strtotime($result->completed_at)) : '-';
                $nomor = method_exists($results, 'firstItem') ? ($results->firstItem() ?? 1) + $index : $index + 1;
                $history = ($attemptHistory ?? collect())->get((string) $result->id_soal, collect());
                $attemptCount = $history->count();
                $canRetry = !$isExam && $attemptCount < 3;
            @endphp

            <tr>
                <td class="center">
                    {{ $nomor }}
                </td>

                <td>
                    {{ $result->paket }}
                    @if (!$isExam)
                        <div class="text-muted" style="font-size:10pt; margin-top:3px;">
                            Percobaan terakhir:
                            {{ $result->attempt_no }}
                            dari 3
                        </div>
                    @endif
                </td>

                <td>
                    @if ($isExam)
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
                    @if (!$isExam)
                        <div class="result-action-buttons">
                            <a href="{{ route('siswa.results.detail', [
                                'id' => $result->id_soal,
                                'attempt' => $result->attempt_no,
                            ]) }}"
                                class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i>
                                Review
                            </a>

                            @if ($canRetry)
                                <a href="{{ route('siswa.training', $result->id_soal) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa fa-repeat"></i>
                                    Ulangi
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-default" disabled>
                                    Maksimal 3x
                                </button>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">
                            Tidak Tersedia
                        </span>
                    @endif
                </td>
            </tr>

            @if (!$isExam && $history->count())
                <tr>
                    <td colspan="8" style="background:#fafbfd;">
                        <details>
                            <summary class="attempt-history-toggle">
                                <i class="fa fa-history"></i>

                                <span>
                                    Histori Percobaan
                                    ({{ $attemptCount }}/3)
                                </span>

                                <i class="fa fa-chevron-down attempt-history-chevron"></i>
                            </summary>

                            <div style="margin-top:10px;">
                                <table
                                    class="
                                        table
                                        table-bordered
                                        table-condensed
                                    "
                                    style="
                                        margin-bottom:0;
                                        background:#fff;
                                    ">
                                    <thead>
                                        <tr>
                                            <th>
                                                Percobaan
                                            </th>

                                            <th class="center">
                                                Nilai
                                            </th>

                                            <th class="center">
                                                Status
                                            </th>

                                            <th>
                                                Tanggal
                                            </th>

                                            <th class="center" style="min-width: 200px;">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($history as $historyAttempt)
                                            @php
                                                $historyScore = (float) $historyAttempt->score;
                                                $historyPassed = $historyScore >= (float) $result->kkm;
                                            @endphp

                                            <tr>
                                                <td>
                                                    Percobaan
                                                    {{ $historyAttempt->attempt_no }}
                                                </td>

                                                <td class="center">
                                                    {{ rtrim(rtrim(number_format($historyScore, 2, '.', ''), '0'), '.') }}
                                                </td>

                                                <td class="center">
                                                    @if ($historyPassed)
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
                                                    {{ $historyAttempt->finished_at ? $historyAttempt->finished_at->format('d-m-Y H:i') : '-' }}
                                                </td>

                                                <td class="center">
                                                    <a href="{{ route('siswa.results.detail', [
                                                        'id' => $result->id_soal,
                                                        'attempt' => $historyAttempt->attempt_no,
                                                    ]) }}"
                                                        class="
                                                            btn
                                                            btn-xs
                                                            btn-info
                                                            attempt-history-view
                                                        ">
                                                        <i class="fa fa-eye"></i>

                                                        Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    </td>
                </tr>
            @endif

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
