@extends('layouts/siswa_baru')
@section('title', 'Review Hasil Ujian')
@section('breadcrumb')
  <li><a href="{{ url('/siswa') }}">Home</a></li>
  <li><a href="{{ url('/hasil-siswa') }}">Hasil Ujian</a></li>
  <li class="active">Review Jawaban</li>
@endsection

@section('content')
<style type="text/css">
  .review-summary {
    margin-bottom: 20px;
  }

  .review-stat {
    border: solid 1px #e0e6ed;
    background: #fff;
    padding: 15px;
    min-height: 92px;
    margin-bottom: 10px;
    text-align: center;
  }

  .review-stat .value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    margin-top: 5px;
  }

  .review-question {
    border: solid 1px #dfe5ec;
    background: #fff;
    margin-bottom: 20px;
  }

  .review-question-header {
    padding: 12px 15px;
    border-bottom: solid 1px #dfe5ec;
    background: #f7f9fb;
    font-weight: bold;
  }

  .review-question-body {
    padding: 15px;
  }

  .review-question-text {
    margin-bottom: 15px;
    font-size: 15px;
  }

  .review-option {
    position: relative;
    border: solid 1px #e1e5ea;
    border-radius: 3px;
    margin-bottom: 8px;
    padding: 10px 12px;
    background: #fff;
  }

  .review-option-code {
    display: inline-block;
    width: 30px;
    vertical-align: top;
    font-weight: bold;
  }

  .review-option-text {
    display: inline-block;
    width: calc(100% - 40px);
    vertical-align: top;
  }

  .review-option.correct-answer,
  .review-option.selected-correct {
    border-color: #4caf50;
    background: #eef9ef;
  }

  .review-option.wrong-answer {
    border-color: #e53935;
    background: #fff0f0;
  }

  .review-option-badge {
    margin-top: 8px;
  }

  .review-meta {
    margin-top: 15px;
    padding-top: 12px;
    border-top: solid 1px #e6e6e6;
  }

  .review-status-correct {
    color: #198754;
    font-weight: bold;
  }

  .review-status-wrong {
    color: #d32f2f;
    font-weight: bold;
  }

  .review-status-empty {
    color: #777;
    font-weight: bold;
  }

  .review-back {
    margin-bottom: 15px;
  }
</style>

<div class="col-md-12">
  <div class="review-back">
    <a href="{{ url('/hasil-siswa') }}" class="btn btn-default">
      <i class="fa fa-chevron-left"></i> Kembali ke Hasil Ujian
    </a>
  </div>

  <div class="card review-summary">
    <div class="card-header bg-white">
      <div class="media">
        <div class="media-body">
          <h4 class="card-title">Review Hasil Ujian</h4>
          <p class="card-subtitle">{{ $soal->paket }}</p>
        </div>
      </div>
    </div>

    <div style="padding: 15px;">
      <table class="table table-bordered table-condensed">
        <tbody>
          <tr>
            <td style="width: 160px;"><strong>Paket Soal</strong></td>
            <td>{{ $soal->paket }}</td>
          </tr>
          <tr>
            <td><strong>Deskripsi</strong></td>
            <td>{{ $soal->deskripsi }}</td>
          </tr>
          <tr>
            <td><strong>Jenis</strong></td>
            <td>{{ $jenis }}</td>
          </tr>
          <tr>
            <td><strong>KKM</strong></td>
            <td>{{ $soal->kkm }}</td>
          </tr>
          <tr>
            <td><strong>Status</strong></td>
            <td>
              @if($lulus)
                <span style="color:#009900; font-size:18px; font-weight:bold;">Lulus</span>
              @else
                <span style="color:#e60000; font-size:18px; font-weight:bold;">Tidak Lulus</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>

      <div class="row">
        <div class="col-sm-3 col-xs-6">
          <div class="review-stat">
            Nilai
            <span class="value">{{ $nilai }}</span>
          </div>
        </div>

        <div class="col-sm-3 col-xs-6">
          <div class="review-stat">
            Benar
            <span class="value" style="color:#198754;">{{ $benar }}</span>
          </div>
        </div>

        <div class="col-sm-3 col-xs-6">
          <div class="review-stat">
            Salah
            <span class="value" style="color:#d32f2f;">{{ $salah }}</span>
          </div>
        </div>

        <div class="col-sm-3 col-xs-6">
          <div class="review-stat">
            Tidak Dijawab
            <span class="value" style="color:#777;">{{ $tidakDijawab }}</span>
          </div>
        </div>
      </div>

      <div class="alert alert-info" style="margin-bottom:0;">
        Total soal: <strong>{{ $jumlahSoal }}</strong>.
        Warna hijau menunjukkan kunci jawaban yang benar. Jika jawaban Anda salah,
        pilihan Anda ditandai warna merah.
      </div>
    </div>
  </div>

  @if($jawabs->count())
    <?php $no = 1; ?>

    @foreach($jawabs as $jawab)
      <?php
        $pilihan = strtoupper(trim((string) $jawab->jawaban));
        $kunci = strtoupper(trim((string) $jawab->kunci));
        $isAnswered = ($pilihan !== '');
        $isCorrect = ($isAnswered && $pilihan === $kunci);

        $options = array(
          'A' => $jawab->pila,
          'B' => $jawab->pilb,
          'C' => $jawab->pilc,
          'D' => $jawab->pild,
          'E' => $jawab->pile
        );
      ?>

      <div class="review-question">
        <div class="review-question-header">
          <span>Soal {{ $no++ }}</span>

          <span class="pull-right">
            @if(!$isAnswered)
              <span class="review-status-empty">
                <i class="fa fa-minus-circle"></i> Tidak Dijawab
              </span>
            @elseif($isCorrect)
              <span class="review-status-correct">
                <i class="fa fa-check-circle"></i> Benar
              </span>
            @else
              <span class="review-status-wrong">
                <i class="fa fa-times-circle"></i> Salah
              </span>
            @endif
          </span>

          <div class="clearfix"></div>
        </div>

        <div class="review-question-body">
          <div class="review-question-text">
            {!! $jawab->soal !!}
          </div>

          @foreach($options as $kode => $teks)
            <?php
              $optionClass = 'review-option';

              if ($kode === $kunci) {
                $optionClass .= ' correct-answer';
              }

              if ($kode === $pilihan && $pilihan !== $kunci) {
                $optionClass .= ' wrong-answer';
              }

              if ($kode === $pilihan && $pilihan === $kunci) {
                $optionClass .= ' selected-correct';
              }
            ?>

            <div class="{{ $optionClass }}">
              <span class="review-option-code">{{ $kode }}.</span>
              <span class="review-option-text">{!! $teks !!}</span>

              <div class="review-option-badge">
                @if($kode === $kunci && $kode === $pilihan)
                  <span class="label label-success">
                    <i class="fa fa-check"></i> Jawaban Anda &amp; Kunci Jawaban
                  </span>
                @elseif($kode === $kunci)
                  <span class="label label-success">
                    <i class="fa fa-check"></i> Kunci Jawaban
                  </span>
                @elseif($kode === $pilihan)
                  <span class="label label-danger">
                    <i class="fa fa-times"></i> Jawaban Anda
                  </span>
                @endif
              </div>
            </div>
          @endforeach

          <div class="review-meta">
            <div class="row">
              <div class="col-sm-4">
                <strong>Jawaban Anda:</strong>
                @if($isAnswered)
                  {{ $pilihan }}
                @else
                  <span class="text-muted">-</span>
                @endif
              </div>

              <div class="col-sm-4">
                <strong>Kunci Jawaban:</strong> {{ $kunci }}
              </div>

              <div class="col-sm-4">
                <strong>Score:</strong>
                {{ (float) $jawab->score_diperoleh }} / {{ (float) $jawab->max_score }}
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @else
    <div class="alert alert-warning">
      Detail soal tidak tersedia untuk paket ini.
    </div>
  @endif

  <div style="margin-bottom: 25px;">
    <a href="{{ url('/hasil-siswa') }}" class="btn btn-default">
      <i class="fa fa-chevron-left"></i> Kembali ke Hasil Ujian
    </a>
  </div>
</div>
@endsection
