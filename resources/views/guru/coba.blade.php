
<?php
  if (Auth::user()->email != "") {
?>
    <hr class="prettyline"><br>
    <center>
    <h1><b>Selamat Datang</b></h1>
    <h3>Silahkan Login untuk mengakses halaman Anda</h3>
    <h4><a href="{{ url('/auth/logout') }}">Logout</a></h4>
    <em>{{ $tgl }}</em>
    <br>
    <button class="btn btn-primary btn-lg" href="#signup" data-toggle="modal" data-target=".bs-modal-sm" style="margin: 15px 0 0 0;"><span class="glyphicon glyphicon-lock"></span> Login</button>
    </center>
    <br>
      <hr class="prettyline">
   </div>
    
  <!-- Modal -->
  <div class="modal fade bs-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
          <br>
          <div class="bs-example bs-example-tabs">
              <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#signin" data-toggle="tab">Sign In</a></li>
                <li class=""><a href="#why" data-toggle="tab">About?</a></li>
              </ul>
          </div>
        <div class="modal-body">
          <div id="myTabContent" class="tab-content">
          <div class="tab-pane fade in" id="why">
          <p>Aplikasi ujian ini di kembangkan dengan desain responsive, sehingga bisa diakses dengan baik oleh berbagai perangkat.</p>
          <p></p><br> Jika mengalami kendala silahkan sampaikan ke <a mailto:href="support@tipa.co.id"></a>Support@Tipa.co.id</a> untuk mendapatkan bantuan teknis.</p>
          </div>
          <div class="tab-pane fade active in" id="signin">
              <form class="form-horizontal">
              <fieldset>
              <div class="control-group">
                <label class="control-label" for="userid">Email:</label>
                <div class="controls">
                  <input required="" id="userid" name="userid" type="text" class="form-control" placeholder="JoeSixpack" class="input-medium" required="">
                </div>
              </div>

              <!-- Password input-->
              <div class="control-group">
                <label class="control-label" for="passwordinput">Password:</label>
                <div class="controls">
                  <input required="" id="passwordinput" name="passwordinput" class="form-control" type="password" placeholder="********" class="input-medium">
                </div>
              </div>

              <!-- Multiple Checkboxes (inline) -->
              <div class="control-group">
                <label class="control-label" for="rememberme">&nbsp;</label>
                <div class="controls">
                  <label class="checkbox inline" for="rememberme-0">
                    <input type="checkbox" name="rememberme" id="rememberme-0" value="Remember me" style="margin: 0 0 0 0;">
                    <span style="margin: 0 0 0 25px;">Remember me</span>
                  </label>
                </div>
              </div>

              <!-- Button -->
              <div class="control-group">
                <label class="control-label" for="signin"></label>
                <div class="controls">
                  <button id="signin" name="signin" class="btn btn-success">Sign In</button>
                </div>
              </div>
              </fieldset>
              </form>
          </div>
          
      </div>
        </div>
        <div class="modal-footer">
        <center>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </center>
        </div>
      </div>
    </div>
  </div>
  <?php
    }else{
      echo "selamat datang";
    }
  ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    