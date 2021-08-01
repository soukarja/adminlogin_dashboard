<?php
require 'dependencies.php';
$id = 0;
if (executeQuery("DESCRIBE `$table_name`") && countRowsInTable($table_name) > 0) {
  $path = explode('/', $_SERVER['REQUEST_URI']);
  if (strtolower($path[count($path) - 2]) == "invite") {
    $id = (int) $path[count($path) - 1];
    if (countRows("SELECT * FROM `$table_name` WHERE id = $id") <= 0) {
      header("location: $auth_url");
      exit;
    } else {
      $data = fetchData("SELECT * FROM `$table_name` WHERE id = $id");
      if ($data["password"] != '') {
        header("location: $auth_url");
        exit;
      }
    }
  } else if (!isLoggedIn()) {
    header("location: $auth_url");
    exit;
  }
} else {
  $roles = [];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>New User</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

  <link rel="stylesheet" href="<?php echo $auth_url ?>/css/style.css" />
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
                  <h3 class="mb-0">New User</h3>
                  <span class="text-danger" id="errmsg"></span>
                </div>
                <div class="w-10">
                  <p class="social-media d-flex justify-content-end">
                    <?php

                    if (isLoggedIn()) {
                      if ($id == 0) {
                        echo '<button title="Copy Invite Link" id="copyLink" class="social-icon d-flex align-items-center justify-content-center" style="outline: none;"><span class="fa fa-clone"></span></button>';
                      } else {
                        echo '<button title="Delete Invite Link" id="deleteLink" class="social-icon d-flex align-items-center justify-content-center" style="outline: none;"><span class="fa fa-trash"></span></button>';
                      }
                    }

                    ?>
                  </p>
                </div>
              </div>
              <form action="#" class="signin-form">
                <div class="form-group mb-3">
                  <label class="label" for="name">Username</label>
                  <input type="text" class="form-control" placeholder="Username" id="username" <?php echo trim($data['username']) == "" ? "required" : "disabled value='" . trim($data['username']) . "'" ?> />
                </div>
                <?php

                if (count($roles) > 0) {
                  echo '<div class="form-group mb-3">
  <label class="label" for="name">User Role</label>
  <select name="role" id="role" class="form-control" ' . ($id != 0 ? "disabled" : "") . '>';

                  $x = 1;
                  foreach ($roles as $role) {
                    echo "<option value='" . $x++ . "'>$role</option>";
                  }

                  echo '</select>
</div>';

                  if ($id != 0) {
                    echo '<script>document.querySelector("#role").value ="' . $data['role'] . '";</script>';
                  }
                }


                ?>

                <div class="form-group mb-3">
                  <label class="label" for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Password" id="password" required />
                </div>
                <div class="form-group mb-3">
                  <label class="label" for="repeatpassword">Repeat Password</label>
                  <input type="password" class="form-control" placeholder="Password" id="repeatpassword" required />
                </div>
                <div class="form-group">
                  <button type="submit" class="form-control btn btn-primary submit px-3">
                    Create Account
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="<?php echo $auth_url ?>/js/jquery.min.js"></script>
  <script src="<?php echo $auth_url ?>/js/popper.js"></script>
  <script src="<?php echo $auth_url ?>/js/bootstrap.min.js"></script>
  <script src="<?php echo $auth_url ?>/js/main.js"></script>
</body>

</html>


<script>
  var copyId = "";


  $("#copyLink").click(function() {
    var username = $("#username").val().trim();
    var role = "";
    <?php

    if (count($roles) > 0) {
      echo 'role = $("#role").val();';
    }

    ?>

    $("#errmsg").html("");
    if (copyId == "") {
      $.post('<?php echo $auth_url ?>/functions.php', {
        action: "copyLink",
        username: username,
        role: role
      }, function(data) {
        if (!data.includes('Error')) {
          copyId = data;
          copyText(data);
        } else {
          $("#errmsg").html(data);
        }
      });
    } else {
      copyText(copyId);
    }
  });


  $("#deleteLink").click(function() {
    var errMsgBox = $("#errmsg");

    if (confirm("Do you really want to delete this invite link?")) {
      $.post('<?php echo $auth_url ?>/functions.php', {
        action: "deleteLink",
        id: <?php echo $id; ?>
      }, function(data) {
        if (data == "success")
          window.open('<?php echo $auth_url . "/?msg=" . urlencode("Link Deleted Successfully") ?>', '_self');
        else
          errMsgBox.html(data);
      });
    }

  });

  function copyText(link) {

    var code = document.createElement('input');
    code.value = link;
    document.querySelector('body').append(code);
    code.select();
    code.setSelectionRange(0, 9999999); /* For mobile devices */

    /* Copy the text inside the text field */
    document.execCommand("copy");

    code.remove();

    $("#errmsg").html("Link copied to clipboard");
  }

  $('form').submit(function(e) {
    e.preventDefault();
    // alert("a");
    var username = $("#username").val().trim();
    var password = $("#password").val();
    var repeatpassword = $("#repeatpassword").val();
    var errMsgBox = $("#errmsg");
    errMsgBox.html("");

    var role = "";
    <?php

    if (count($roles) > 0) {
      echo 'role = $("#role").val();';
    }

    ?>

    if (username == "") {
      errMsgBox.text("Please Enter a Username");
      return;
    }
    if (password.length < 6) {
      errMsgBox.text("Password should be at least 6 characters");
      return;
    }
    if (password != repeatpassword) {
      errMsgBox.text("Passwords do not match");
      return;
    }
    $.post('<?php echo $auth_url ?>/functions.php', {
      action: "newuser",
      username: username,
      password: password,
      role: role,
      id: <?php echo $id; ?>
    }, function(data) {
      if (data == "success")
        window.open('<?php echo $auth_url . "/?msg=" . urlencode("Account Created Successfully") ?>', '_self');
      else
        errMsgBox.html(data);
    });
  });
</script>