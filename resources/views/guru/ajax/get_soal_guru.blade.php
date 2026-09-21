@if ($soals->count())

    @include(
        'guru.partials.soal_table',
        [
            'soals' => $soals
        ]
    )

@else

    <div class="alert alert-danger">

        <b>ERROR:</b>

        Paket soal dengan kata kunci

        "<b>{{ $q }}</b>"

        tidak ditemukan.

    </div>

@endif