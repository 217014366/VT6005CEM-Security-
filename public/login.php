<?php

require "../private/autoload.php";
include("simple-php-captcha.php");

$emailErr = $passwordErr = $captchaErr = "";
$email = $password = "";

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] == $_POST['token']) {

  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    if (!preg_match("/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/", $email)) {
      $emailErr = "Please enter a valid email";
    }
  }

  if (empty($_POST["password"])) {
    $passwordErr = "Password is required";
  } else {
    $password = test_input($_POST["password"]);

    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
      $passwordErr = "Password must contain at least eight characters, at least one number and both lower and uppercase letters and special characters";
    }
  }

  if (empty($_POST["form_captcha"])) {
    $captchaErr = "Captcha validation is required";
  } else {
    $form_captcha = test_input($_POST['form_captcha']);

    if ($_SESSION['captcha']['code'] == $_POST['form_captcha']) {
      // Validation: Checking entered captcha code with the generated captcha code
      $captchaErr = "";
    } else {
      $captchaErr = "Wrong Captcha";
    }
  }


  if ($emailErr == "" && $passwordErr == "" && $captchaErr == "") {
    $search_sql = $connection->prepare("SELECT salt, hash FROM applicants where email = ? ");
    $search_sql->bind_param("s", $email);
    $search_sql->execute();
    $search_sql->store_result();

    // print_r($search_sql);

    if ($search_sql->num_rows > 0) {
      $search_sql->bind_result($salt, $hash);
      $search_sql->fetch();
      // Compute hash value using salt in database and password from user input
      $passwordhash = hash("sha512", $salt . $password);

      /* Check whether the hash value in database and the hase value 
    computed in step 6 are the same */
      if (strcmp($hash, $passwordhash) == 0) {
        echo "<h2>Authentication success!</h2>";
        header("Location: booking.php");

      } else {
        echo "<h2>The password is wrong, authentication failed</h2>";
      }
    } else {
      echo "<h2>User name not exist, authentication failed</h2>";
    }
  }
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

$_SESSION = array();
$_SESSION['captcha'] = simple_php_captcha();
$_SESSION['email'] = $email;
$_SESSION['token'] = generateSalt(60);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>

  <div class="full-screen-container">
    <div class="login-container">
      <h1 class="login-title">Online Appointment Booking for Replacement of ID Cards</h1><br>
      <h1 class="login-title2">Log In Page</h1>

      <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <?php if ($emailErr == "") {
          echo '<div class="input-group success">';
        } else {
          echo '<div class="input-group error">';
        } ?>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" require>
        <span class="msg"><?php echo $emailErr; ?></span>
    </div>

    <?php if ($passwordErr == "") {
      echo '<div class="input-group success">';
    } else {
      echo '<div class="input-group error">';
    } ?>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" require>
    <span class="msg"><?php echo $passwordErr; ?></span>
  </div>

  <?php
  echo '<div style="display: flex;justify-content: space-between;">';
  if ($captchaErr == "") {
    echo '<div class="input-group success" style="width:50%" >';
  } else {
    echo '<div class="input-group error" style="width:50%" >';
  }
  echo '<label for="captcha">Captcha</label>';
  echo '<input type="text" name="form_captcha">';
  echo '<span class="msg">' . $captchaErr . '</span></div>';
  echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code" height="100px">';
  echo '</div>';
  ?>
  <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
  <button type="submit" name="Login" class="login-button">Log In</button>
  </form><br>
  <a><?php echo ("<button onclick=\"location.href='signup.php'\" name='login' class='other-button2' style='float: right;'> Sign Up Page</button>"); ?></a>

  </div>
  </div>
</body>

</html>