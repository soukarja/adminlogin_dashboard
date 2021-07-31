<?php

require 'dependencies.php';

if (!executeQuery("DESCRIBE `$table_name`") || countRowsInTable($table_name) <= 0) {
  header("Location: new-user");
  exit;
}


if (isLoggedIn()) {
  header("location: $success_link");
  exit;
}

$msg = "";
if (isset($_GET['msg']))
  $msg = trim(urldecode($_GET['msg']));


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <section class="ftco-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="wrap d-md-flex">
            <div class="login-wrap p-4 p-lg-5">
              <div class="d-flex">
                <div class="w-100 mb-4">
                  <h3 class="mb-0">Sign In</h3>
                  <span class="text-danger" id="errmssg"><?php echo $msg; ?></span>
                </div>
              </div>
              <form action="#" class="signin-form">
                <div class="form-group mb-3">
                  <label class="label" for="name">Username</label>
                  <input type="text" class="form-control" placeholder="Username" id="username" required />
                </div>
                <div class="form-group mb-3">
                  <label class="label" for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Password" id="password" required />
                </div>
                <div class="form-group">
                  <button type="submit" class="form-control btn btn-primary submit px-3">
                    Sign In
                  </button>
                </div>
                <!-- <div class="form-group d-md-flex">
                    <div class="w-50 text-left">
                      <label class="checkbox-wrap checkbox-primary mb-0"
                        >Remember Me
                        <input type="checkbox" checked />
                        <span class="checkmark"></span>
                      </label>
                    </div>
                    <div class="w-50 text-md-right">
                      <a href="#">Forgot Password</a>
                    </div>
                  </div> -->
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>

<script>
  $('form').submit(function(e) {
    e.preventDefault();
    var username = $("#username").val().trim();
    var password = $("#password").val();
    $("#errmssg").html("");

    $.post('<?php echo $auth_url ?>/functions.php', {
      username: username,
      password: password,
      action: "login"
    }, function(data) {
      if (data == "success") {
        window.open("<?php echo $success_link ?>", '_self');
      } else {
        $("#errmssg").html(data);
      }
    })
  });
</script>