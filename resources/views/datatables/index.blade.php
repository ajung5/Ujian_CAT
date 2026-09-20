<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel DataTables Tutorial</title>

        <!-- Bootstrap CSS -->
        <link href="{!! url('vendor/twbs/bootstrap/dist/css/bootstrap.css') !!}" rel="stylesheet">
        <link href="{!! url('css/jquery.dataTables.min.css') !!}" rel="stylesheet">

        <style>
            body {
                padding-top: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <table class="table table-bordered" id="users-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Title</th>
                        <th>Author name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- jQuery -->
        <script src="{!! url('vendor/twbs/bootstrap/dist/js/jquery.min.js') !!}"></script>
        <script src="{!! url('js/jquery.dataTables.min.js') !!}"></script>
        <script src="{!! url('vendor/twbs/bootstrap/dist/js/bootstrap.min.js') !!}"></script>

        <script>
        $(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('datatables.data') !!}',
                columns: [
                    {data: 'id', name: 'soals.id'},
                    {data: 'paket', name: 'soals.paket'},
                    {data: 'nama', name: 'users.nama'},
                    {data: 'created_at', name: 'soals.created_at'},
                    {data: 'updated_at', name: 'soals.updated_at'}
                ]
            });
        });
        </script>
    </body>
</html>