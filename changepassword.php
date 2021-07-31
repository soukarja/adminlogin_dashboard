<?php
require 'dependencies.php';

if (!isLoggedIn()) {
  header("Location: $auth_url");
  exit;
}


$id = $_SESSION[$session_key_userID];
$data = fetchData("SELECT * FROM `$table_name` WHERE `id` = $id");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Change Password</title>
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
                <div class="w-100 mb-4 d-flex flex-column">
                  <h3 class="mb-0">Change Password</h3>
                  <h6>for <span style="font-style: italic; color: green;">@<?php echo $data['username']; ?></span></h6>
                  <span class="text-danger" id="errmsg"></span>
                </div>
              </div>
              <form action="#" class="signin-form">

                <div class="form-group mb-3">
                  <label class="label" for="password">New Password</label>
                  <input type="password" class="form-control" placeholder="Password" id="password" required />
                </div>
                <div class="form-group mb-3">
                  <label class="label" for="repeatpassword">Repeat New Password</label>
                  <input type="password" class="form-control" placeholder="Password" id="repeatpassword" required />
                </div>
                <div class="form-group">
                  <button type="submit" class="form-control btn btn-primary submit px-3">
                    Change Password
                  </button>
                </div>
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
  $("form").submit(function(e) {
    e.preventDefault();

    var password = $("#password").val();
    var repeatpassword = $("#repeatpassword").val();
    var errMsgBox = $("#errmsg");
    errMsgBox.html("");


    if (password.length < 6) {
      errMsgBox.text("Password should be at least 6 characters");
      return;
    }
    if (password != repeatpassword) {
      errMsgBox.text("Passwords do not match");
      return;
    }


    $.post('<?php echo $auth_url ?>/functions.php', {
      action: "changepass",
      password: password,
      id: <?php echo $id; ?>
    }, function(data) {
      if (data == "success")
        window.open('<?php echo $auth_url."/?msg=".urlencode("Password Changed Successfully") ?>', '_self');
      else
        errMsgBox.html(data);
    });
  });
</script>