@extends('layouts.siswa_baru')

@section('title', 'Detail Soal' . ' - ' . $assessmentLabel)

@push('styles')
    <style>
        #defaultCountdown {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            color: #003284;
        }

        .question-grid {
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 10px;
            width: 100%;
            padding: 12px 0;
            background: #fff;
        }

        .question-grid .page {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            height: 48px;
            padding: 0;
            margin: 0;
            border: 1px solid #c0c0c0;
            border-radius: 4px;
            background: #e9e9e9;
            color: #717171;
            font-weight: bold;
            font-size: 18px;
            line-height: 1;
            text-decoration: none;
        }

        .page.active {
            background: #616161;
            color: #fff;
        }

        .page.current {
            outline: 3px solid #003284;
            outline-offset: 2px;
        }

        .benar {
            padding: 15px;
            background: #045ff2;
            color: #fff;
        }

        #question-navigation {
            margin-top: 15px;
            padding: 12px 0;
            border-top: 1px solid #e3e9f2;
        }

        .question-actions {
            position: sticky;
            bottom: 0;
            z-index: 10;
            display: flex;
            justify-content: flex-end;
            padding: 12px 0;
            border-top: 1px solid #e3e9f2;
            background: #fff;
        }

        .question-actions #kirim {
            min-width: 110px;
        }

        .finish-confirm-overlay {
            position: fixed;
            z-index: 10020;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.55);
        }

        .finish-confirm-box {
            width: 100%;
            max-width: 460px;
            padding: 28px;
            border-radius: 8px;
            background: #fff;
            text-align: center;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
        }

        .finish-confirm-icon {
            margin-bottom: 15px;
            color: #d9534f;
            font-size: 42px;
        }

        .finish-confirm-box h4 {
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: bold;
        }

        .finish-confirm-box p {
            margin-bottom: 15px;
        }

        .finish-confirm-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .finish-confirm-actions .btn {
            min-width: 130px;
        }

        @media (max-width: 767px) {
            .finish-confirm-box {
                padding: 22px 18px;
            }

            .finish-confirm-actions {
                flex-direction: column;
            }

            .finish-confirm-actions .btn {
                width: 100%;
            }

            .question-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 8px;
            }

            .question-grid .page {
                height: 44px;
                font-size: 16px;
            }
        }
    </style>
@endpush

@section('breadcrumb')

    <li>
        <a href="{{ route('siswa.index') }}">
            Home
        </a>
    </li>

    <li>
        <a href="{{ $backUrl }}">
            {{ $isTraining ? 'Latihan Materi' : 'Soal Ujian' }}
        </a>
    </li>

    <li class="active">
        Detail {{ $assessmentLabel }}
    </li>

@endsection

@section('content')

    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <div class="media">
                    <div class="media-body">
                        <h4 class="card-title">
                            Detail {{ $assessmentLabel }}
                        </h4>
                    </div>
                </div>
            </div>

            <div style="padding:15px;">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width:110px;">
                                Paket Soal
                            </td>

                            <td style="width:15px;">
                                :
                            </td>

                            <td>
                                {{ $soal->paket }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Deskripsi
                            </td>

                            <td>
                                :
                            </td>

                            <td>
                                {{ $soal->deskripsi }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Jumlah
                            </td>

                            <td>
                                :
                            </td>

                            <td>
                                {{ count($questionOrder) }}
                                soal
                            </td>
                        </tr>

                        <tr>
                            <td>
                                KKM
                            </td>

                            <td>
                                :
                            </td>

                            <td>
                                {{ $soal->kkm }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Waktu
                            </td>

                            <td>
                                :
                            </td>

                            <td>
                                {{ number_format(((int) $soal->waktu) / 60, 0) }}
                                menit

                                @if ($hasStarted)
                                    <br>
                                    <strong>
                                        Sisa waktu sekitar
                                        {{ ceil($remainingSeconds / 60) }}
                                        menit.
                                    </strong>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="alert alert-info">
                    <p
                        style="
                        font-size:18px;
                        margin-bottom:10px;
                    ">
                        Sebelum mulai mengerjakan,
                        baca dan ikuti instruksi
                        berikut:
                    </p>

                    <ul>
                        <li>
                            Jangan refresh halaman
                            selama ujian jika tidak
                            diperlukan.
                        </li>

                        <li>
                            Waktu tetap berjalan
                            setelah ujian dimulai.
                        </li>

                        <li>
                            Jawaban disimpan setiap
                            kali Anda memilih opsi.
                        </li>

                        <li>
                            Saat waktu habis,
                            jawaban akan difinalisasi
                            otomatis.
                        </li>
                    </ul>
                </div>

                <button type="button" id="trigger_soal"
                    class="
                    btn
                    btn-primary
                    btn-lg
                ">
                    {{ $hasStarted ? 'Lanjutkan ' . $assessmentLabel : 'Mulai ' . $assessmentLabel }}
                </button>
            </div>
        </div>
    </div>

    <div id="wrap_soal"
        style="
        display:none;
        position:fixed;
        z-index:9999;
        top:0;
        left:0;
        background:#f2f7ff;
        height:100%;
        width:100%;
        overflow-y:auto;
    ">
        <div class="container" id="wrap-siap-ujian"
            style="
            margin:50px auto 0 auto;
            padding:15px;
            background:#fff;
            text-align:center;
        ">
            <p>
                Dengan mengklik tombol

                <b>
                    Siap {{ $assessmentLabel }}
                </b>,

                waktu {{ strtolower($assessmentLabel) }}
                akan berjalan.
            </p>

            @if ($hasStarted)
                <div class="alert alert-warning">
                    {{ $assessmentLabel }}
                    ini sudah pernah dimulai.

                    Waktu tetap berjalan walaupun
                    halaman ditutup.
                </div>
            @endif

            <button type="button" id="siap-ujian" class="btn btn-success">
                {{ $hasStarted ? 'Lanjutkan ' . $assessmentLabel : 'Siap ' . $assessmentLabel }}
            </button>

            <button type="button" id="batal-ujian" class="btn btn-danger">
                Batal
            </button>
        </div>

        <div class="
            container-fluid
            wrap_ujian
        " style="display:none;">
            <div class="row">
                <div class="col-md-12"
                    style="
                    background:#003284;
                    padding:10px;
                    color:#fff;
                ">
                    Hai {{ $user->nama }},
                    Selamat mengerjakan
                    {{ $soal->paket }}
                </div>
            </div>
        </div>

        <div class="
            container
            wrap_ujian
        " style="display:none;">
            <div style="height:15px;"></div>

            <div class="row">
                <div class="
                    col-md-8
                    col-sm-12
                "
                    style="
                    padding:
                    0 10px 0 0;
                ">
                    <div id="wrap-soal"
                        style="
                        border:
                        1px solid #e3e9f2;
                        background:#fff;
                        padding:10px;
                        min-height:200px;
                    ">
                        <div class="
                            text-center
                            text-muted
                        "
                            style="padding:50px;">
                            Memuat soal...
                        </div>
                    </div>

                    <div id="question-navigation">
                        <button type="button" id="soal-sebelumnya" class="btn btn-default">
                            <i
                                class="
                                fa
                                fa-chevron-left
                            "></i>

                            Sebelumnya
                        </button>

                        <button type="button" id="soal-berikutnya"
                            class="
                            btn
                            btn-default
                            pull-right
                        ">
                            Berikutnya

                            <i
                                class="
                                fa
                                fa-chevron-right
                            "></i>
                        </button>

                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="
                    col-md-4
                    col-sm-12
                ">
                    <div class="card">
                        <div
                            class="
                            card-header
                            bg-white
                        ">
                            <div id="defaultCountdown">
                                --:--:--
                            </div>
                        </div>

                        <div
                            class="
                            card-header
                            bg-white
                        ">
                            <h4 class="card-title">
                                Nomor Soal
                            </h4>

                            <div id="finish-confirm-overlay" class="finish-confirm-overlay" style="display:none;">
                                <div class="finish-confirm-box">
                                    <div class="finish-confirm-icon">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </div>

                                    <h4>
                                        Selesaikan {{ $assessmentLabel }}?
                                    </h4>

                                    <p id="finish-confirm-message">
                                        Pastikan seluruh jawaban sudah diperiksa.
                                    </p>

                                    <div id="finish-unanswered-warning" class="alert alert-warning" style="display:none;">
                                        Masih ada
                                        <strong id="finish-unanswered-count">0</strong>
                                        soal yang belum dijawab.
                                    </div>

                                    <p class="text-muted">
                                        Setelah {{ strtolower($assessmentLabel) }} diselesaikan,
                                        jawaban tidak dapat diubah kembali.
                                    </p>

                                    <div class="finish-confirm-actions">
                                        <button type="button" id="batal-selesai" class="btn btn-default">
                                            Batal
                                        </button>

                                        <button type="button" id="konfirmasi-selesai" class="btn btn-danger">
                                            <i class="fa fa-check"></i>
                                            Ya, Selesaikan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="padding:0 15px;">
                            <div class="question-grid">
                                @foreach ($questionOrder as $index => $questionId)
                                    <a href="#"
                                        class="
                                        page
                                        question-number
                                        {{ in_array($questionId, $answeredIds, true) ? 'active' : '' }}
                                    "
                                        id="get-soal{{ $questionId }}" data-question-id="{{ $questionId }}">
                                        {{ $index + 1 }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="question-actions">
                                <button type="button" id="kirim" class="btn btn-danger">
                                    <i class="fa fa-check-circle"></i>
                                    Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            'use strict';


            const examId =
                {{ (int) $soal->id }};


            const questionIds =
                @json(array_values($questionOrder));


            const answeredIds =
                @json(array_values($answeredIds));


            let currentQuestionId =
                null;

            let remainingSeconds =
                {{ (int) $remainingSeconds }};

            let timerInterval =
                null;

            let syncInterval =
                null;

            let isSavingAnswer =
                false;

            let isLoadingQuestion =
                false;

            let examFinished =
                false;


            function formatTime(seconds) {

                seconds =
                    Math.max(
                        0,
                        parseInt(
                            seconds,
                            10
                        ) || 0
                    );


                const hours =
                    Math.floor(
                        seconds / 3600
                    );

                const minutes =
                    Math.floor(
                        (seconds % 3600) /
                        60
                    );

                const secs =
                    seconds % 60;


                return [
                    String(hours)
                    .padStart(2, '0'),

                    String(minutes)
                    .padStart(2, '0'),

                    String(secs)
                    .padStart(2, '0')

                ].join(':');

            }


            function renderTimer() {

                $('#defaultCountdown')
                    .text(
                        formatTime(
                            remainingSeconds
                        )
                    );

            }


            function getCurrentIndex() {

                return questionIds
                    .indexOf(
                        parseInt(
                            currentQuestionId,
                            10
                        )
                    );

            }


            function updateNavigation() {

                const index =
                    getCurrentIndex();


                $('.question-number')
                    .removeClass('current');


                if (
                    currentQuestionId !== null
                ) {
                    $('#get-soal' +
                        currentQuestionId
                    ).addClass('current');
                }


                $('#soal-sebelumnya')
                    .prop(
                        'disabled',
                        index <= 0 ||
                        isSavingAnswer ||
                        isLoadingQuestion
                    );


                $('#soal-berikutnya')
                    .prop(
                        'disabled',
                        index < 0 ||
                        index >=
                        questionIds.length - 1 ||
                        isSavingAnswer ||
                        isLoadingQuestion
                    );

            }


            function handleExpired(
                response
            ) {

                if (examFinished) {
                    return;
                }


                examFinished = true;


                clearInterval(
                    timerInterval
                );

                clearInterval(
                    syncInterval
                );


                alert(
                    response.message ||
                    'Waktu ujian telah habis.'
                );


                window.location.href =
                    response.redirect ||
                    '{{ route('siswa.results') }}';

            }


            function loadQuestion(
                questionId
            ) {

                questionId =
                    parseInt(
                        questionId,
                        10
                    );


                if (
                    isLoadingQuestion ||
                    isSavingAnswer ||
                    !questionIds.includes(
                        questionId
                    )
                ) {
                    return;
                }


                isLoadingQuestion =
                    true;

                updateNavigation();


                $.ajax({

                    type: 'POST',

                    url: '{{ url('/get-soal') }}/' +
                        questionId,

                    success: function(html) {

                        currentQuestionId =
                            questionId;


                        $('#wrap-soal')
                            .hide()
                            .html(html)
                            .fadeIn(200);


                        isLoadingQuestion =
                            false;

                        updateNavigation();

                    },


                    error: function(xhr) {

                        isLoadingQuestion =
                            false;

                        updateNavigation();


                        if (
                            xhr.status === 409
                        ) {

                            handleExpired({

                                message: xhr.responseJSON
                                    ?.message ||
                                    '{{ $assessmentLabel }} sudah berakhir.',

                                redirect: '{{ route('siswa.results') }}'

                            });

                            return;

                        }


                        alert(
                            'Soal gagal dimuat.'
                        );

                    }

                });

            }


            function firstQuestionToOpen() {

                for (
                    const id of questionIds
                ) {

                    if (
                        !answeredIds.includes(
                            id
                        )
                    ) {
                        return id;
                    }

                }


                return questionIds[0];

            }


            function syncTimer() {

                if (examFinished) {
                    return;
                }


                $.ajax({

                    type: 'POST',

                    url: '{{ route('siswa.exam.time') }}',

                    data: {
                        id_soal: examId
                    },


                    success: function(response) {

                        if (
                            response.expired ||
                            response.finished
                        ) {

                            handleExpired(
                                response
                            );

                            return;
                        }


                        remainingSeconds =
                            parseInt(
                                response
                                .remaining_seconds,
                                10
                            );

                        renderTimer();

                    },


                    error: function(xhr) {

                        if (
                            xhr.status === 409 &&
                            xhr.responseJSON
                        ) {

                            handleExpired(
                                xhr.responseJSON
                            );

                        }

                    }

                });

            }


            function startLocalTimer() {

                clearInterval(
                    timerInterval
                );

                clearInterval(
                    syncInterval
                );


                renderTimer();


                timerInterval =
                    setInterval(
                        function() {

                            remainingSeconds--;

                            renderTimer();


                            if (
                                remainingSeconds <= 0
                            ) {

                                clearInterval(
                                    timerInterval
                                );

                                syncTimer();

                            }

                        },
                        1000
                    );


                /*
                 * Server menjadi sumber waktu utama.
                 */
                syncInterval =
                    setInterval(
                        syncTimer,
                        10000
                    );

            }


            $('#trigger_soal')
                .on(
                    'click',
                    function() {

                        const element =
                            document.getElementById(
                                'wrap_soal'
                            );


                        $('#wrap_soal')
                            .show();


                        if (
                            element
                            .requestFullscreen
                        ) {

                            element
                                .requestFullscreen()
                                .catch(
                                    function() {
                                        /*
                                         * Fullscreen gagal bukan
                                         * alasan menghentikan ujian.
                                         */
                                    }
                                );

                        }

                    }
                );


            $('#batal-ujian')
                .on(
                    'click',
                    function() {

                        window.location.href =
                            @json($backUrl);

                    }
                );


            $('#siap-ujian')
                .on(
                    'click',
                    function() {

                        const button =
                            $(this);


                        button.prop(
                            'disabled',
                            true
                        );


                        $.ajax({

                            type: 'POST',

                            url: @json($startUrl),


                            success: function(
                                response
                            ) {

                                remainingSeconds =
                                    parseInt(
                                        response
                                        .remaining_seconds,
                                        10
                                    );


                                $('#wrap-siap-ujian')
                                    .hide();


                                $('.wrap_ujian')
                                    .fadeIn(250);


                                startLocalTimer();


                                loadQuestion(
                                    firstQuestionToOpen()
                                );

                            },


                            error: function(xhr) {

                                button.prop(
                                    'disabled',
                                    false
                                );


                                if (
                                    xhr.responseJSON &&
                                    (
                                        xhr.responseJSON
                                        .expired ||
                                        xhr.responseJSON
                                        .finished
                                    )
                                ) {

                                    handleExpired(
                                        xhr.responseJSON
                                    );

                                    return;

                                }


                                alert(
                                    xhr.responseJSON
                                    ?.message ||
                                    'Ujian gagal dimulai.'
                                );

                            }

                        });

                    }
                );


            $(document)
                .on(
                    'click',
                    '.question-number',
                    function(event) {

                        event.preventDefault();


                        loadQuestion(
                            $(this)
                            .data(
                                'question-id'
                            )
                        );

                    }
                );


            $('#soal-sebelumnya')
                .on(
                    'click',
                    function() {

                        const index =
                            getCurrentIndex();


                        if (index > 0) {

                            loadQuestion(
                                questionIds[
                                    index - 1
                                ]
                            );

                        }

                    }
                );


            $('#soal-berikutnya')
                .on(
                    'click',
                    function() {

                        const index =
                            getCurrentIndex();


                        if (
                            index >= 0 &&
                            index <
                            questionIds.length - 1
                        ) {

                            loadQuestion(
                                questionIds[
                                    index + 1
                                ]
                            );

                        }

                    }
                );


            $(document)
                .on(
                    'change',
                    '#wrap-soal input[type=radio]',
                    function() {

                        if (
                            isSavingAnswer ||
                            isLoadingQuestion
                        ) {
                            return;
                        }


                        const radio =
                            $(this);

                        const container =
                            $('#wrap-soal');


                        const pilihan =
                            radio.val();

                        const idSoal =
                            container
                            .find(
                                '.question-id-soal'
                            )
                            .first()
                            .val();

                        const detailId =
                            container
                            .find(
                                '.question-detail-id'
                            )
                            .first()
                            .val();


                        isSavingAnswer =
                            true;


                        container
                            .find(
                                'input[type=radio]'
                            )
                            .prop(
                                'disabled',
                                true
                            );


                        updateNavigation();


                        $.ajax({

                            type: 'POST',

                            url: '{{ route('siswa.exam.answer') }}',

                            data: {

                                pilihan: pilihan,

                                id_soal: idSoal,

                                no_soal_id: detailId

                            },


                            success: function(
                                response
                            ) {

                                container
                                    .find(
                                        'tr[id^="wrap_pil_"]'
                                    )
                                    .removeClass(
                                        'benar'
                                    );


                                radio
                                    .closest('tr')
                                    .addClass(
                                        'benar'
                                    );


                                $('#get-soal' +
                                        detailId
                                    )
                                    .addClass(
                                        'active'
                                    );


                                if (
                                    !answeredIds
                                    .includes(
                                        parseInt(
                                            detailId,
                                            10
                                        )
                                    )
                                ) {

                                    answeredIds
                                        .push(
                                            parseInt(
                                                detailId,
                                                10
                                            )
                                        );

                                }


                                remainingSeconds =
                                    parseInt(
                                        response
                                        .remaining_seconds,
                                        10
                                    );


                                renderTimer();


                                isSavingAnswer =
                                    false;


                                container
                                    .find(
                                        'input[type=radio]'
                                    )
                                    .prop(
                                        'disabled',
                                        false
                                    );


                                updateNavigation();


                                const index =
                                    getCurrentIndex();


                                if (
                                    index >= 0 &&
                                    index <
                                    questionIds.length - 1
                                ) {

                                    setTimeout(
                                        function() {

                                            loadQuestion(
                                                questionIds[
                                                    index + 1
                                                ]
                                            );

                                        },
                                        300
                                    );

                                }

                            },


                            error: function(xhr) {

                                isSavingAnswer =
                                    false;


                                container
                                    .find(
                                        'input[type=radio]'
                                    )
                                    .prop(
                                        'disabled',
                                        false
                                    );


                                updateNavigation();


                                if (
                                    xhr.responseJSON &&
                                    xhr.responseJSON
                                    .expired
                                ) {

                                    handleExpired(
                                        xhr.responseJSON
                                    );

                                    return;

                                }


                                alert(
                                    xhr.responseJSON
                                    ?.message ||
                                    'Jawaban gagal disimpan.'
                                );

                            }

                        });

                    }
                );


            function finishExam() {

                if (examFinished) {
                    return;
                }

                examFinished = true;

                $('#konfirmasi-selesai')
                    .prop('disabled', true);

                $('#batal-selesai')
                    .prop('disabled', true);

                $('#kirim')
                    .prop('disabled', true);

                $.ajax({

                    type: 'POST',

                    url: '{{ route('siswa.exam.finish') }}',

                    data: {
                        id_soal: examId
                    },

                    success: function(response) {

                        clearInterval(timerInterval);
                        clearInterval(syncInterval);

                        window.location.href =
                            response.redirect ||
                            '{{ route('siswa.results') }}';

                    },

                    error: function(xhr) {

                        examFinished = false;

                        $('#konfirmasi-selesai')
                            .prop('disabled', false);

                        $('#batal-selesai')
                            .prop('disabled', false);

                        $('#kirim')
                            .prop('disabled', false);

                        $('#finish-confirm-overlay')
                            .hide();

                        alert(
                            xhr.responseJSON?.message ||
                            'Jawaban gagal dikirim.'
                        );

                    }

                });

            }


            $('#kirim').on('click', function() {

                if (
                    examFinished ||
                    isSavingAnswer ||
                    isLoadingQuestion
                ) {
                    return;
                }

                const unansweredIds =
                    questionIds.filter(
                        function(questionId) {
                            return !answeredIds.includes(
                                parseInt(questionId, 10)
                            );
                        }
                    );

                const unansweredCount =
                    unansweredIds.length;

                if (unansweredCount > 0) {

                    $('#finish-unanswered-count')
                        .text(unansweredCount);

                    $('#finish-unanswered-warning')
                        .show();

                    $('#finish-confirm-message')
                        .text(
                            'Anda masih memiliki soal yang belum dijawab.'
                        );

                } else {

                    $('#finish-unanswered-warning')
                        .hide();

                    $('#finish-confirm-message')
                        .text(
                            'Semua soal sudah dijawab. Pastikan jawaban Anda sudah benar.'
                        );

                }

                $('#finish-confirm-overlay')
                    .css('display', 'flex');

            });


            $('#batal-selesai').on('click', function() {

                $('#finish-confirm-overlay')
                    .hide();

            });


            $('#konfirmasi-selesai').on('click', function() {

                finishExam();

            });


            /*
             * Saat browser keluar fullscreen,
             * ujian tetap berjalan.
             */
            document.addEventListener(
                'fullscreenchange',
                function() {

                    if (
                        examFinished
                    ) {
                        return;
                    }


                    $('#wrap_soal')
                        .show();

                }
            );


            renderTimer();

        });
    </script>
@endpush
