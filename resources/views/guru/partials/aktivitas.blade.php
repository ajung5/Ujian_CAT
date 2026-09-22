<div class="col-sm-12 col-md-4 col-lg-4 dash-right">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                Aktifitas Terkini
            </h4>
        </div>

        <div class="panel-body">
            <ul class="media-list user-list">
                @forelse ($aktifitas as $data)
                    @php
                        $gambarAktifitas = !empty($data->gambar) ? $data->gambar : 'noimage.jpg';
                    @endphp

                    <li class="media">
                        <div class="media-left">
                            <img class="
                                    media-object
                                    img-thumbnail
                                "
                                src="{{ asset('img/' . $gambarAktifitas) }}" alt="{{ $data->nama_user }}">
                        </div>

                        <div class="media-body">
                            <h4 class="media-heading nomargin">
                                {{ $data->nama_user }}
                            </h4>

                            {{ $data->nama }}

                            <small class="date">
                                <i class="fa fa-clock-o"></i>

                                {{ $data->created_at ? \Illuminate\Support\Carbon::parse($data->created_at)->format('d M Y') : '-' }}
                            </small>
                        </div>
                    </li>

                @empty

                    <li class="media">
                        Belum ada aktivitas.
                    </li>
                @endforelse
            </ul>

            <a href="{{ url('/aktifitas') }}" class="btn btn-success"
                style="
                    display:block;
                    width:100%;
                    margin-top:10px;
                ">
                Selengkapnya
            </a>
        </div>
    </div>
</div>
