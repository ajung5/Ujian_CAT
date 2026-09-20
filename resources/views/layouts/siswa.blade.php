<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ ('img/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Selamat Datang {{ $user->nama }}</title>

    <link href="{!! url('vendor/twbs/bootstrap/dist/css/bootstrap.min.css') !!}" rel="stylesheet">
    <link href="{!! url('vendor/twbs/bootstrap/dist/css/style_log.css') !!}" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle navbar-toggle-sidebar collapsed">
        MENU
        </button>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">
          Apps
        </a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">      
        <form class="navbar-form navbar-left" method="GET" role="search">
          <div class="form-group">
            <input type="text" name="q" class="form-control" placeholder="Search">
          </div>
          <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <!-- <li><a href="http://www.pingpong-labs.com" target="_blank">Visit Site</a></li> -->
          <li class="dropdown ">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              Account
              <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li class="dropdown-header">SETTINGS</li>
                <li class=""><a href="{{ url('/profil-siswa') }}">Profil</a></li>
                <li class="divider"></li>
                <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div class="container">
    	@yield('content')
	  </div>
	<script src="{!! url('vendor/twbs/bootstrap/dist/js/jquery.min.js') !!}"></script>
  <script src="{!! url('vendor/twbs/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
  <script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.backstretch("{{ url('/img/bg_siswa.jpg') }}", {speed: 150});
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    $(function () {
      $('.navbar-toggle-sidebar').click(function () {
        $('.navbar-nav').toggleClass('slide-in');
        $('.side-body').toggleClass('body-slide-in');
        $('#search').removeClass('in').addClass('collapse').slideUp(200);
      });

      $('#search-trigger').click(function () {
        $('.navbar-nav').removeClass('slide-in');
        $('.side-body').removeClass('body-slide-in');
        $('.search-input').focus();
      });
    });
    $(document).ready(function() {
      var panels = $('.user-infos');
      var panelsButton = $('.dropdown-user');
      panels.hide();

      panelsButton.click(function() {
          //get data-for attribute
          var dataFor = $(this).attr('data-for');
          var idFor = $(dataFor);
          //current button
          var currentButton = $(this);
          idFor.slideToggle(400, function() {
              //Completed slidetoggle
              if(idFor.is(':visible'))
              {
                  currentButton.html('<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
              }
              else
              {
                  currentButton.html('<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
              }
          })
      });


      $('[data-toggle="tooltip"]').tooltip();

      $("#btnwrap").click(function(){
        //return false;
      });

      $("#loaderupdate").hide();
      $("#updatebenar").hide();
      $("#updatesalah").hide();

      $("#btnupdate").click(function() {
        $("#loaderupdate").show();
        var nama = $("#nama").val();
        var nis = $("#nis").val();
        var jk = $("#jk").val();
        var password = $("#password").val();
        var datastring = "nama="+nama+"&nis="+nis+"&jk="+jk+"&password="+password;
        $.ajax({
          type: "POST",
          url: "{{ url('/updateprofil') }}",
          data: datastring,
          success: function(data){
            if(data == "berhasil"){
              $("#loaderupdate").hide();
              $("#updatesalah").hide();
              $("#updatebenar").show();
              // $("#btnwrap").click();

              $("#submit").hide();
            }else{
              $("#loaderupdate").hide();
              $("#updatebenar").hide();
              $("#updatesalah").html(data).show();
            }
          }
        });

        return false;
      });
    });

    $(document).ready(function (e) {
      $("#uploadimage").on('submit',(function(e) {
        e.preventDefault();
        $("#message").empty();
        $('#loading').show();
        $.ajax({
          url: "{{ url('/updateprofilfoto') }}", // Url to which the request is send
          type: "POST",             // Type of request to be send, called as method
          data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
          contentType: false,       // The content type used when sending data to the server.
          cache: false,             // To unable request pages to be cached
          processData:false,        // To send DOMDocument or non processed data file it is set to false
          success: function(data)   // A function to be called if request succeeds
          {
            $('#loading').hide();
            $("#message").html(data);
          }
        });
      }));

      // Function to preview image after validation
      $(function() {
        $("#file").change(function() {
          $("#message").empty(); // To remove the previous error message
          var file = this.files[0];
          var imagefile = file.type;
          var match= ["image/jpeg","image/png","image/jpg"];
          if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
          {
            $('#previewing').attr('src','noimage.png');
            $("#message").html("<p id='error'>Harap gunakan jenis gambar yang valid</p>"+"<h4>Catatan</h4>"+"<span id='error_message'>Hanya jpeg, jpg dan png saja type yang di anggap valid</span>");
            return false;
          }else{
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
          }
        });
      });
      function imageIsLoaded(e) {
        $("#file").css("color","green");
        $('#image_preview').css("display", "block");
        $('#previewing').attr('src', e.target.result);
        $('#previewing').attr('width', '250px');
        $('#previewing').attr('height', '230px');
      };

      
    });
  </script>
  </body>
</html>