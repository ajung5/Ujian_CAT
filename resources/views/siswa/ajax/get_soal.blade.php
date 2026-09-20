<?php
  if ($cek_jawaban != "") {
    $pilihan = $cek_jawaban->pilihan;
  }else{
    $pilihan = 'ER';
  }
?>

<table class="table table-condensed" style="padding:0; margin: 0">
  <tbody>
    <tr>
      <input type="hidden" name="id_soaljawab" id="id_soaljawab" value="{{ $detailsoal->id_soal }}">
      <input type="hidden" class="question-id-soal" value="{{ $detailsoal->id_soal }}">
      <input type="hidden" class="question-detail-id" value="{{ $detailsoal->id }}">
      <input type="hidden" name="id_soal{{ $detailsoal->id }}" id="id_soal{{ $detailsoal->id }}" value="{{ $detailsoal->id_soal }}">
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

    <tr id="wrap_pil_a" <?php if ($pilihan == 'A') { echo "class='benar'"; } ?>>
      <td style="width: 10px">
        <input type="radio"
               name="pilih{{ $detailsoal->id }}"
               value="A"
               data-toggle="tooltip"
               title="Klik untuk menjawab."
               <?php if ($pilihan == 'A') { echo "checked"; } ?>>
      </td>
      <td>{!! $detailsoal->pila !!}</td>
    </tr>

    <tr id="wrap_pil_b" <?php if ($pilihan == 'B') { echo "class='benar'"; } ?>>
      <td>
        <input type="radio"
               name="pilih{{ $detailsoal->id }}"
               value="B"
               data-toggle="tooltip"
               title="Klik untuk menjawab."
               <?php if ($pilihan == 'B') { echo "checked"; } ?>>
      </td>
      <td>{!! $detailsoal->pilb !!}</td>
    </tr>

    <tr id="wrap_pil_c" <?php if ($pilihan == 'C') { echo "class='benar'"; } ?>>
      <td>
        <input type="radio"
               name="pilih{{ $detailsoal->id }}"
               value="C"
               data-toggle="tooltip"
               title="Klik untuk menjawab."
               <?php if ($pilihan == 'C') { echo "checked"; } ?>>
      </td>
      <td>{!! $detailsoal->pilc !!}</td>
    </tr>

    <tr id="wrap_pil_d" <?php if ($pilihan == 'D') { echo "class='benar'"; } ?>>
      <td>
        <input type="radio"
               name="pilih{{ $detailsoal->id }}"
               value="D"
               data-toggle="tooltip"
               title="Klik untuk menjawab."
               <?php if ($pilihan == 'D') { echo "checked"; } ?>>
      </td>
      <td>{!! $detailsoal->pild !!}</td>
    </tr>

    <tr id="wrap_pil_e" <?php if ($pilihan == 'E') { echo "class='benar'"; } ?>>
      <td>
        <input type="radio"
               name="pilih{{ $detailsoal->id }}"
               value="E"
               data-toggle="tooltip"
               title="Klik untuk menjawab."
               <?php if ($pilihan == 'E') { echo "checked"; } ?>>
      </td>
      <td>{!! $detailsoal->pile !!}</td>
    </tr>
  </tbody>
</table>
