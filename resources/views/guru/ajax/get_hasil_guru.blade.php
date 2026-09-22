<table
    class="
        table
        table-bordered
        table-striped
        table-hover
        table-condensed
    ">
    <thead>
        <tr>
            <th width="55">
                #
            </th>

            <th>
                Paket
            </th>

            <th>
                Jenis
            </th>

            <th>
                Deskripsi
            </th>

            <th>
                KKM
            </th>

            <th>
                Waktu
            </th>

            <th>
                Peserta
            </th>

            <th>
                Tgl Dibuat
            </th>

            <th width="80">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody>
        @forelse ($jawabs
        as $index => $jawab)
            @php

                $nomor = method_exists($jawabs, 'firstItem') ? ($jawabs->firstItem() ?? 1) + $index : $index + 1;
                $jenis = (string) $jawab->jenis === '2' ? 'Latihan' : 'Ujian';
                $tanggal = $jawab->created_at
                    ? \Illuminate\Support\Carbon::parse($jawab->created_at)->translatedFormat('d M Y')
                    : '-';
            @endphp

            <tr>
                <td>
                    {{ $nomor }}
                </td>

                <td>
                    {{ $jawab->paket }}
                </td>

                <td>
                    {{ $jenis }}
                </td>

                <td>
                    {{ $jawab->deskripsi }}
                </td>

                <td>
                    {{ $jawab->kkm }}
                </td>

                <td>
                    {{ round(((int) $jawab->waktu) / 60) }}
                    menit
                </td>

                <td class="text-center">
                    {{ $jawab->jumlah_peserta }}
                </td>

                <td>
                    {{ $tanggal }}
                </td>

                <td>
                    <a href="{{ route('guru.results.detail', $jawab->id_soal) }}" class="btn btn-xs btn-primary">
                        <i class="fa fa-search"></i>

                        Detail
                    </a>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9" class="alert alert-danger">
                    Belum ada data
                    untuk ditampilkan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($jawabs instanceof \Illuminate\Pagination\LengthAwarePaginator && $jawabs->lastPage() > 1)
    {!! $jawabs->links() !!}
@endif
