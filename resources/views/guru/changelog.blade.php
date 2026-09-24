@extends('layouts.guru_baru')

@section('title', 'Changelog - Ujian CAT')

@push('styles')
    <style>
        .changelog-header {
            margin-bottom: 20px;
        }

        .changelog-header h3 {
            margin-top: 0;
            margin-bottom: 6px;
        }

        .changelog-current-version {
            display: inline-block;
            padding: 7px 14px;
            font-size: 14px;
        }

        .changelog-info {
            margin-bottom: 25px;
        }

        .changelog-document {
            line-height: 1.7;
        }

        .changelog-document>h1:first-child {
            display: none;
        }

        .changelog-document h2 {
            margin-top: 35px;
            margin-bottom: 18px;
            padding: 14px 18px;
            border-left: 4px solid #337ab7;
            background: #f7f9fc;
            border-radius: 3px;
            font-size: 21px;
            font-weight: 600;
        }

        .changelog-document h2:first-of-type {
            margin-top: 5px;
        }

        .changelog-document h3 {
            margin-top: 22px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 600;
        }

        .changelog-document ul {
            padding-left: 22px;
            margin-bottom: 18px;
        }

        .changelog-document li {
            margin-bottom: 6px;
        }

        .changelog-document code {
            padding: 2px 5px;
            border-radius: 3px;
            background: #f2f4f7;
            color: #333;
        }

        .changelog-document pre {
            padding: 14px;
            border: 1px solid #e4e7eb;
            border-radius: 4px;
            background: #f7f9fc;
        }

        .changelog-document hr {
            margin-top: 30px;
            margin-bottom: 30px;
            border-color: #e8e8e8;
        }

        .changelog-legend {
            margin-top: 10px;
        }

        .changelog-legend .label {
            display: inline-block;
            margin-right: 5px;
            padding: 5px 8px;
        }
    </style>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row changelog-header">
                    <div class="col-sm-8">
                        <h3 class="panel-title">
                            <i class="fa fa-history"></i>
                            Riwayat Perubahan Aplikasi
                        </h3>

                        <small>
                            Ujian CAT
                        </small>
                    </div>

                    <div class="col-sm-4 text-right">
                        <span class="label label-success changelog-current-version">
                            v{{ $version }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="alert alert-info changelog-info">
                    <i class="fa fa-info-circle"></i>

                    Halaman ini menampilkan histori perubahan aplikasi
                    sejak versi legacy hingga versi saat ini.

                    Riwayat perubahan bersumber dari
                    <code>CHANGELOG.md</code>
                    dan tercatat dalam version control Git.
                </div>

                <div class="changelog-legend">
                    <span class="label label-success">
                        Added
                    </span>

                    <span class="label label-primary">
                        Changed
                    </span>

                    <span class="label label-warning">
                        Fixed
                    </span>

                    <span class="label label-danger">
                        Security
                    </span>
                </div>

                <hr>

                <div class="changelog-document">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
