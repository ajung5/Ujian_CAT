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
                Percobaan
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

                $historyModalId = 'attempt-history-modal-' . $result->id_soal;
            @endphp

            <tr>
                <td class="center">
                    {{ $nomor }}
                </td>

                <td>
                    {{ $result->paket }}
                    @if (!$isExam)
                        <div class="text-muted"
                            style="
                                font-size:10pt;
                                margin-top:3px;
                            ">
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
                    @if (!$isExam && $attemptCount > 0)
                        <button type="button"
                            class="
                                btn
                                btn-default
                                btn-sm
                                attempt-history-button
                            "
                            data-toggle="modal" data-target="#{{ $historyModalId }}">
                            <i class="fa fa-history"></i>

                            Riwayat
                            {{ $attemptCount }}/3
                        </button>
                    @else
                        <span class="text-muted">
                            -
                        </span>
                    @endif
                </td>

                <td class="center">
                    @if ($isExam)
                        <span class="text-muted">
                            -
                        </span>
                    @elseif ($canRetry)
                        <a href="{{ route('siswa.training', $result->id_soal) }}"
                            class="
                                btn
                                btn-sm
                                btn-primary
                                result-retry-button
                            ">
                            <i class="fa fa-repeat"></i>

                            Ulangi
                        </a>
                    @else
                        <span class="attempt-limit-status">
                            <i class="fa fa-lock"></i>

                            Batas 3x
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="alert alert-info">
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

{{-- Modal Riwayat Percobaan --}}
@foreach ($results as $result)
    @php
        $isExam = (int) $result->jenis_soal === 1;

        $history = ($attemptHistory ?? collect())->get((string) $result->id_soal, collect());

        $historyModalId = 'attempt-history-modal-' . $result->id_soal;
    @endphp

    @if (!$isExam && $history->count())
        <div class="
                modal
                fade
                attempt-history-modal
            "
            id="{{ $historyModalId }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="
                    modal-dialog
                    modal-lg
                "
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>

                        <h4 class="modal-title">
                            <i class="fa fa-history"></i>

                            Riwayat Percobaan
                        </h4>

                        <div class="attempt-history-package">
                            {{ $result->paket }}
                        </div>
                    </div>

                    <div class="modal-body">
                        <div class="attempt-history-summary">
                            <span>
                                Total percobaan
                            </span>

                            <strong>
                                {{ $history->count() }}/3
                            </strong>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="
                                    table
                                    table-bordered
                                    table-hover
                                    attempt-history-table
                                ">
                                <thead>
                                    <tr>
                                        <th class="center">
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

                                        <th class="center">
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
                                            <td class="center">
                                                <strong>
                                                    Percobaan
                                                    {{ $historyAttempt->attempt_no }}
                                                </strong>
                                            </td>

                                            <td class="center">
                                                {{ rtrim(rtrim(number_format($historyScore, 2, '.', ''), '0'), '.') }}
                                            </td>

                                            <td class="center">
                                                @if ($historyPassed)
                                                    <span class="history-status history-status-success">
                                                        <i class="fa fa-check-circle"></i>

                                                        Lulus
                                                    </span>
                                                @else
                                                    <span class="history-status history-status-failed">
                                                        <i class="fa fa-times-circle"></i>

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
                                                        btn-sm
                                                        btn-info
                                                        attempt-review-button
                                                    ">
                                                    <i class="fa fa-eye"></i>

                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
