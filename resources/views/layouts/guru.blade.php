<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Selamat Datang {{ $user->nama }}</title>
<link rel="stylesheet" href="{{ url('lib/fontawesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ url('css/admin.css') }}">
<link rel="icon" href="{{ ('img/favicon.png') }}">
<script src="{{url('js/modernizr.js')}}"></script>
<link rel="stylesheet" href="{{url('lib/Hover/hover.css')}}">
<link rel="stylesheet" href="{{url('lib/weather-icons/css/weather-icons.css')}}">
<link rel="stylesheet" href="{{url('lib/jquery-toggles/toggles-full.css')}}">
<link rel="stylesheet" href="{{url('lib/morrisjs/morris.css')}}">
</head>
<body>
<header>
  <div class="headerpanel">
    <div class="logopanel">
      <h2><a href="{{ url('/admin') }}">Ujian</a></h2>
    </div>
    <div class="headerbar">

      <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>

      <div class="searchpanel">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for...">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
          </span>
        </div><!-- input-group -->
      </div>
      <div class="header-right">
        <ul class="headermenu">
          <li>
            <div id="noticePanel" class="btn-group">
              <button class="btn btn-notice alert-notice" data-toggle="dropdown">
                <i class="fa fa-globe"></i>
              </button>
              <div id="noticeDropdown" class="dropdown-menu dm-notice pull-right">
                <div role="tabpanel">
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="notification">
                      <ul class="list-group notice-list">
                        <li class="list-group-item unread">
                          <div class="row">
                            <div class="col-xs-2">
                              <i class="fa fa-envelope"></i>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">New message from Weno Carasbong</a></h5>
                              <small>June 20, 2015</small>
                              <span>Soluta nobis est eligendi optio cumque...</span>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item unread">
                          <div class="row">
                            <div class="col-xs-2">
                              <i class="fa fa-user"></i>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">Renov Leonga is now following you!</a></h5>
                              <small>June 18, 2015</small>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <i class="fa fa-user"></i>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">Zaham Sindil is now following you!</a></h5>
                              <small>June 17, 2015</small>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <i class="fa fa-thumbs-up"></i>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">Rey Reslaba likes your post!</a></h5>
                              <small>June 16, 2015</small>
                              <span>HTML5 For Beginners Chapter 1</span>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <i class="fa fa-comment"></i>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">Socrates commented on your post!</a></h5>
                              <small>June 16, 2015</small>
                              <span>Temporibus autem et aut officiis debitis...</span>
                            </div>
                          </div>
                        </li>
                      </ul>
                      <a class="btn-more" href="#">View More Notifications <i class="fa fa-long-arrow-right"></i></a>
                    </div><!-- tab-pane -->

                    <div role="tabpanel" class="tab-pane" id="reminders">
                      <h1 id="todayDay" class="today-day">...</h1>
                      <h3 id="todayDate" class="today-date">...</h3>

                      <h5 class="today-weather"><i class="wi wi-hail"></i> Cloudy 77 Degree</h5>
                      <p>Thunderstorm in the area this afternoon through this evening</p>

                      <h4 class="panel-title">Upcoming Events</h4>
                      <ul class="list-group">
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <h4>20</h4>
                              <p>Aug</p>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">HTML5/CSS3 Live! United States</a></h5>
                              <small>San Francisco, CA</small>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <h4>05</h4>
                              <p>Sep</p>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">Web Technology Summit</a></h5>
                              <small>Sydney, Australia</small>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <h4>25</h4>
                              <p>Sep</p>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">HTML5 Developer Conference 2015</a></h5>
                              <small>Los Angeles CA United States</small>
                            </div>
                          </div>
                        </li>
                        <li class="list-group-item">
                          <div class="row">
                            <div class="col-xs-2">
                              <h4>10</h4>
                              <p>Oct</p>
                            </div>
                            <div class="col-xs-10">
                              <h5><a href="#">AngularJS Conference 2015</a></h5>
                              <small>Silicon Valley CA, United States</small>
                            </div>
                          </div>
                        </li>
                      </ul>
                      <a class="btn-more" href="#">View More Events <i class="fa fa-long-arrow-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li>
            <div class="btn-group">
              <button type="button" class="btn btn-logged" data-toggle="dropdown">
                <?php if ($user->gambar == "") { ?>
                <img src="img/noimage.jpg" alt="foto guru" class="media-object img-circle" />
                <?php }else{ ?>
                <img src="img/{{$user->gambar}}" alt="{{$user->gambar}}" class="media-object img-circle" />
                <?php } ?>
                <?php
                  $namapendek = explode(" ", Auth::user()->nama);
                  echo $namapendek[0];
                ?>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a href="{{ url('/profil-guru') }}"><i class="fa fa-user"></i> Profil</a></li>
                <li><a href="{{ url('/auth/logout') }}"><i class="fa fa-sign-out"></i> Log Out</a></li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>
<section>
  <div>
  	@yield('content')
	</div>
</section>
<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
<script src="{{url('lib/jquery-ui/jquery-ui.js')}}"></script> 
<script src="{{url('lib/bootstrap/js/bootstrap.js')}}"></script> 
<script src="{{url('lib/jquery-toggles/toggles.js')}}"></script> 
<script src="{{url('lib/morrisjs/morris.js')}}"></script> 
<script src="{{url('lib/raphael/raphael.js')}}"></script> 
<script src="{{url('lib/flot/jquery.flot.js')}}"></script> 
<script src="{{url('lib/flot/jquery.flot.resize.js')}}"></script> 
<script src="{{url('lib/flot-spline/jquery.flot.spline.js')}}"></script> 
<script src="{{url('lib/jquery-knob/jquery.knob.js')}}"></script> 
<script src="{{url('js/quirk.js')}}"></script> 
<script src="{{url('js/dashboard.js')}}"></script>
<script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
<script type="text/javascript">
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.backstretch("{{ url('/img/bg_guru.jpg') }}", {speed: 150});
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    $(document).ready(function() {
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
      $('#siswa').select2();
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