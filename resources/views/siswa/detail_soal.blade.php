<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ url('/assets/assets/libs/countdown/css/jquery.countdown.css') }}">
<link rel="stylesheet" href="{{ url('/assets/examples/css/sweetalert.min.css') }}">
<style type="text/css">
  #defaultCountdown { width: 240px; height: 57px; }
  .pagination { background: #fff; color: #000 !important; }

  .page {
    display: inline-block;
    padding: 0px 9px;
    margin: 8px 4px 0 0;
    border-radius: 3px;
    border: solid 1px #c0c0c0;
    background: #e9e9e9;
    box-shadow: inset 0px 1px 0px rgba(255,255,255, .8), 0px 1px 3px rgba(0,0,0, .1);
    font-size: .875em;
    font-weight: bold;
    text-decoration: none;
    color: #717171;
    text-shadow: 0px 1px 0px rgba(255,255,255, 1);
  }

  .page:hover, .page.gradient:hover {
    background: #fefefe;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FEFEFE), to(#f0f0f0));
    background: -moz-linear-gradient(0% 0% 270deg,#FEFEFE, #f0f0f0);
  }

  .page.active {
    border: solid 1px #4a4a4a;
    background: #616161;
    box-shadow: inset 0px 0px 8px rgba(0,0,0, .5), 0px 1px 0px rgba(255,255,255, .8);
    color: #f0f0f0;
    text-shadow: 0px 0px 3px rgba(0,0,0, .5);
  }

  .page.gradient {
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f8f8f8), to(#e9e9e9));
    background: -moz-linear-gradient(0% 0% 270deg,#f8f8f8, #e9e9e9);
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

  input[type=radio] {
    margin-top: 5px;
  }

  #question-navigation {
    margin-top: 15px;
    padding: 12px 0;
    border-top: solid thin #e3e9f2;
  }
</style>
@extends('layouts/siswa_baru')
@section('title', 'Detail Soal')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li><a href="{{ url('/soal-siswa') }}">Soal Ujian</a></li>
  <li class="active">Detail Soal</li>
@endsection
@section('content')
<div class="col-md-12">
  <div class="card">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Detail Soal</h4>
          <p class="card-subtitle"></p>
        </div>
      </div>
    </div>
    <div style="padding: 15px">
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td style="width: 110px">Paket Soal</td>
            <td style="width: 15px">:</td>
            <td>{{ $soal->paket }}</td>
          </tr>
          <tr>
            <td>Deskripsi</td>
            <td>:</td>
            <td>{{ $soal->deskripsi }}</td>
          </tr>
          <tr>
            <td>Jumlah</td>
            <td>:</td>
            <td>{{ $jumlah_soal->count() }} soal</td>
          </tr>
          <tr>
            <td>KKM</td>
            <td>:</td>
            <td>{{ $soal->kkm }}</td>
          </tr>
          <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>
              <?php
                echo $jumlah_menis = $soal->waktu/60;
                echo " menit";
                if($countexamtime == ""){
                  $jam = floor($jumlah_menis/60);
                  $menit = $jumlah_menis % 60;
                }else{
                  echo ', sisa waktu Anda : '.$jam_sisa = substr($countexamtime->waktu/60, 0, 2);
                  echo " menit";
                  $jam = floor($jam_sisa/60);
                  $menit = $jam_sisa % 60;
                }
              ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="alert alert-info">
        <p style="font-size:18px; margin-bottom:10px;">Sebelum mulai mengerjakan, baca dan ikuti instruksi dibawah ini:</p>
        <ul>
          <li><b style="color: #F00;">Jangan</b> <i>refresh</i> halaman Anda.</li>
          <li>Selalu perhatikan waktu ujian, karena sistem akan mengirimkan jawaban secara otomatis saat waktu ujian telah habis.</li>
          <li>Pastikan Anda siap melakukan ujian, karena saat klik tombol <b>"Mulai Ujian"</b> waktu akan berjalan dan tidak bisa dihentikan walaupun server mengalami gangguan.</li>
          <li>Ikutilah petunjuk dari Bpk/Ibu Guru.</li>
        </ul>
        <hr>
        <p class="center"><b>GOOD LUCK</b></p>
      </div>
      <input type="button" value="Mulai Ujian" id="trigger_soal" class="btn btn-primary btn-lg">
    </div>
  </div>
</div>
@endsection

<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{ url('/assets/assets/libs/countdown/js/jquery.plugin.min.js') }}"></script>
<script src="{{ url('/assets/assets/libs/countdown/js/jquery.countdown.js') }}"></script>
<script src="{{ url('/assets/assets/vendor/sweetalert.min.js') }}"></script>

<div id="wrap_soal" style="display: none; background: #f2f7ff; height: 100%; width: 100%; padding: 0; margin: 0; overflow-y: scroll;">
  <div class="container" style="margin: 50px auto 0 auto; padding: 15px; background: #fff; text-align: center" id="wrap-siap-ujian">
    <p>Dengan mengklik tombol "Siap Ujian", waktu ujian akan mulai berjalan. Sistem akan mengirim jawaban Anda saat waktu telah selesai walaupun Anda tidak mengklik Kirim Jawaban.</p>
    <input type="button" id="siap-ujian" value="Siap Ujian" class="btn btn-success">
    <a type="button" href="{{ url('/soal-siswa/'.$soal->id) }}" class="btn btn-danger">Batal</a>
  </div>

  <div class="container-fluid hidden-sm hidden-xs wrap_ujian" style="display: none;">
    <div class="row">
      <div class="col-md-12" style="background: #003284; padding:10px;">
        <div class="row">
          <div class="col-md-6" style="color: #fff;">
            Hai {{ $user->nama }}, Selamat mengerjakan Soal {{ $soal->paket }}
          </div>
          <div class="col-md-6"></div>
        </div>
      </div>
    </div>
  </div>

  <input type="hidden" name="id_soal{{ $detailsoal->id }}" id="id_soal{{ $detailsoal->id }}" value="{{ $detailsoal->id_soal }}">
  <input type="hidden" id="current_question_id" value="{{ $detailsoal->id }}">

  <div class="container wrap_ujian" style="display: none;">
    <div style="height: 15px"></div>
    <div class="row">

      <!-- AREA SOAL -->
      <div class="col-md-8 col-sm-12" style="padding: 0 10px 0 0;">
        <div style="border:solid thin #e3e9f2; background: #fff; padding:10px" id="wrap-soal">
          <table class="table table-condensed" style="padding:0; margin: 0">
            <tbody>
              <tr>
                <input type="hidden" name="id_soaljawab" id="id_soaljawab" value="{{ $detailsoal->id_soal }}">
                <input type="hidden" class="question-id-soal" value="{{ $detailsoal->id_soal }}">
                <input type="hidden" class="question-detail-id" value="{{ $detailsoal->id }}">
                <input type="hidden" name="no_soal_id{{ $detailsoal->id }}" id="no_soal_id{{ $detailsoal->id }}" value="{{ $detailsoal->id }}">

                <td colspan="2">
                  <?php if($detailsoal->audio != ""){ $audio = $detailsoal->audio; ?>
                    <div style="margin: 0 0 20px 0; padding: 15px; border: solid thin #a8a8a8;">
                      <span style="color: #828282">Audio for Listening</span>
                      <hr style="margin: 8px 0 15px 0">
                      <div class="clearfix"></div>
                      <p>
                        <audio controls>
                          <source src="{{ url('/assets/audios/'.$audio) }}" type="audio/mpeg">
                          Your browser does not support the audio element.
                        </audio>
                      </p>
                    </div>
                  <?php } ?>
                  {!! $detailsoal->soal !!}
                </td>
              </tr>

              <tr id="wrap_pil_a">
                <td style="width: 10px"><input type="radio" name="pilih{{ $detailsoal->id }}" value="A" data-toggle='tooltip' title="Klik untuk menjawab."></td>
                <td>{!! $detailsoal->pila !!}</td>
              </tr>
              <tr id="wrap_pil_b">
                <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="B" data-toggle='tooltip' title="Klik untuk menjawab."></td>
                <td>{!! $detailsoal->pilb !!}</td>
              </tr>
              <tr id="wrap_pil_c">
                <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="C" data-toggle='tooltip' title="Klik untuk menjawab."></td>
                <td>{!! $detailsoal->pilc !!}</td>
              </tr>
              <tr id="wrap_pil_d">
                <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="D" data-toggle='tooltip' title="Klik untuk menjawab."></td>
                <td>{!! $detailsoal->pild !!}</td>
              </tr>
              <tr id="wrap_pil_e">
                <td><input type="radio" name="pilih{{ $detailsoal->id }}" value="E" data-toggle='tooltip' title="Klik untuk menjawab."></td>
                <td>{!! $detailsoal->pile !!}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- NAVIGASI BERADA DI BAWAH SOAL -->
        <div id="question-navigation">
          <button type="button" id="soal-sebelumnya" class="btn btn-default">
            <i class="fa fa-chevron-left"></i> Sebelumnya
          </button>

          <button type="button" id="soal-berikutnya" class="btn btn-default pull-right">
            Berikutnya <i class="fa fa-chevron-right"></i>
          </button>

          <div class="clearfix"></div>
        </div>
      </div>

      <!-- PANEL KANAN -->
      <div class="row col-md-4 col-sm-12" style="padding: 0 10px">
        <div class="card">
          <div class="card-header bg-white">
            <div class="media">
              <div class="media-body">
                <h4 class="card-title">
                  <div style="margin: 0 auto;" id="defaultCountdown"></div>
                </h4>
              </div>
            </div>
          </div>

          <div class="card-header bg-white">
            <div class="media">
              <div class="media-body">
                <h4 class="card-title">Nomor Soal</h4>
              </div>
            </div>
          </div>

          <div style="padding: 0 15px">
            <ul class="pagination">
              <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
              <?php $no = 1; ?>
              @if($soals->count())
                @foreach($soals as $data)
                  <a href="#"
                     style="text-decoration: none;"
                     class="page gradient question-number"
                     id="get-soal{{ $data->id }}"
                     data-question-id="{{ $data->id }}">{{ $no++ }}</a>
                @endforeach
              @endif
            </ul>

            <hr>
            <input type="button" id="kirim" value="Selesai" class="btn btn-primary" style="float: right">
            <br style="clear: both;">
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
jQuery.noConflict()(function ($) {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var questionIds = [
    @foreach($soals as $data)
      {{ $data->id }},
    @endforeach
  ];

  var currentQuestionId = parseInt("{{ $detailsoal->id }}", 10);
  var isSavingAnswer = false;
  var isLoadingQuestion = false;

  function getCurrentQuestionIndex() {
    return questionIds.indexOf(parseInt(currentQuestionId, 10));
  }

  function updateQuestionNavigation() {
    var index = getCurrentQuestionIndex();

    $("#current_question_id").val(currentQuestionId);

    $(".question-number").removeClass("current");
    $("#get-soal" + currentQuestionId).addClass("current");

    $("#soal-sebelumnya").prop("disabled", index <= 0 || isLoadingQuestion || isSavingAnswer);
    $("#soal-berikutnya").prop(
      "disabled",
      index < 0 || index >= questionIds.length - 1 || isLoadingQuestion || isSavingAnswer
    );
  }

  function loadQuestion(questionId) {
    questionId = parseInt(questionId, 10);

    if (isSavingAnswer || isLoadingQuestion || questionIds.indexOf(questionId) === -1) {
      return;
    }

    if (questionId === parseInt(currentQuestionId, 10)) {
      updateQuestionNavigation();
      return;
    }

    isLoadingQuestion = true;
    updateQuestionNavigation();

    $.ajax({
      type: "POST",
      url: "{{ url('/get-soal') }}/" + questionId,
      data: {
        id: questionId
      },
      success: function(data) {
        currentQuestionId = questionId;

        $("#wrap-soal")
          .stop(true, true)
          .hide()
          .html(data)
          .fadeIn(200);

        isLoadingQuestion = false;
        updateQuestionNavigation();
      },
      error: function() {
        isLoadingQuestion = false;
        
        $("#wrap-soal input[type=radio]")
        .prop("disabled", false);

        updateQuestionNavigation();

        alert("Soal gagal dimuat. Silakan coba kembali.");
      }
    });
  }

  function goPreviousQuestion() {
    var index = getCurrentQuestionIndex();

    if (index > 0) {
      loadQuestion(questionIds[index - 1]);
    }
  }

  function goNextQuestion() {
    var index = getCurrentQuestionIndex();

    if (index >= 0 && index < questionIds.length - 1) {
      loadQuestion(questionIds[index + 1]);
    }
  }

  function kirimJawaban() {
    if (!confirm('Yakin jawaban akan dikirim?')) {
      return false;
    }

    var id_soal = $("#id_soal{{ $detailsoal->id }}").val();

    $.ajax({
      url: "{{ url('/kirimjawaban') }}",
      type: 'POST',
      data: 'id_soal=' + id_soal,
      success: function() {
        window.location.href = "{{ url('/hasil-siswa') }}";
      },
      error: function() {
        alert("Jawaban gagal dikirim. Silakan coba kembali.");
      }
    });
  }

  $(document).ready(function() {
    updateQuestionNavigation();

    $("#siap-ujian").click(function() {
      $("#wrap-siap-ujian").hide();
      $(".wrap_ujian").fadeIn(250);

      $('#defaultCountdown').countdown({
        until: '+{{ $jam }}h +{{ $menit }}m +0s',
        format: 'HMS',
        onExpiry: liftOff
      });

      // Update sisa waktu ujian setiap 5 detik.
      setInterval(function() {
        var id_soal = $("#id_soal{{ $detailsoal->id }}").val();

        $.ajax({
          url: "{{ url('/countexamtime') }}",
          type: 'POST',
          data: 'id_soal=' + id_soal,
          success: function(data) {
            console.log(data);
          }
        });
      }, 5000);
    });

    function liftOff() {
      alert('Waktu ujian telah selesai. Jawaban Anda akan dikirimkan.');
      kirimJawaban();
    }

    // Navigasi manual: nomor soal.
    $(document).on("click", ".question-number", function(e) {
      e.preventDefault();

      var questionId = parseInt($(this).attr("data-question-id"), 10);
      loadQuestion(questionId);
    });

    // Navigasi manual: tombol sebelumnya.
    $("#soal-sebelumnya").click(function() {
      goPreviousQuestion();
    });

    // Navigasi manual: tombol berikutnya.
    $("#soal-berikutnya").click(function() {
      goNextQuestion();
    });

    // Penyimpanan jawaban terpusat untuk soal awal maupun soal yang dimuat via AJAX.
    $(document).on("change", "#wrap-soal input[type=radio]", function() {
      if (isSavingAnswer || isLoadingQuestion) {
        return;
      }

      var radio = $(this);
      var questionContainer = $("#wrap-soal");
      var pilihan = radio.val();
      var id_soal = questionContainer.find(".question-id-soal").first().val();
      var no_soal_id = questionContainer.find(".question-detail-id").first().val();

      if (!pilihan || !id_soal || !no_soal_id) {
        alert("Data soal tidak lengkap. Silakan muat ulang halaman ujian.");
        return;
      }

      isSavingAnswer = true;
      questionContainer.find("input[type=radio]").prop("disabled", true);
      updateQuestionNavigation();

      $.ajax({
        type: "POST",
        url: "{!! url('simpanjawabankliksiswa') !!}",
        data: {
          pilihan: pilihan,
          id_soal: id_soal,
          no_soal_id: no_soal_id
        },
        success: function(data) {
          questionContainer.find("tr[id^='wrap_pil_']").removeClass("benar");
          radio.closest("tr").addClass("benar");

          // Pertahankan class .page dan hanya hilangkan gradient.
          $("#get-soal" + no_soal_id)
            .removeClass("gradient")
            .addClass("active");

          isSavingAnswer = false;
          updateQuestionNavigation();

          // Otomatis ke soal berikutnya hanya setelah save berhasil.
          // Pada soal terakhir, sistem tetap diam dan tidak auto-submit.
          var index = getCurrentQuestionIndex();
          if (index >= 0 && index < questionIds.length - 1) {
            setTimeout(function() {
              goNextQuestion();
            }, 300);
          } else {
            questionContainer.find("input[type=radio]").prop("disabled", false);
          }
        },
        error: function() {
          isSavingAnswer = false;
          questionContainer.find("input[type=radio]").prop("disabled", false);
          updateQuestionNavigation();
          alert("Jawaban gagal disimpan. Silakan pilih kembali.");
        }
      });
    });

    $("#kirim").click(function() {
      kirimJawaban();
    });
  });

  // Fullscreen API.
  var elem = document.getElementById("wrap_soal");
  var btnelem = document.getElementById("trigger_soal");

  btnelem.addEventListener("click", function() {
    $("#wrap_soal").show();

    if (elem.requestFullscreen) {
      elem.requestFullscreen().catch(function(err) {
        console.error("Gagal masuk fullscreen:", err);
        $("#wrap_soal").show();
      });
    } else if (elem.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen();
    } else {
      console.warn("Browser tidak mendukung Fullscreen API.");
      $("#wrap_soal").show();
    }
  });

  function handleFullscreenChange() {
    var fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement;

    if (fullscreenElement) {
      console.log("Mode fullscreen aktif");
      $("#wrap_soal").show();
    } else {
      console.log("Keluar dari mode fullscreen");
      $("#wrap_soal").show();
    }
  }

  document.addEventListener("fullscreenchange", handleFullscreenChange);
  document.addEventListener("webkitfullscreenchange", handleFullscreenChange);
});
</script>
