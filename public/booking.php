<?php

require "../private/autoload.php";

$email = $_SESSION['email'];
if (!isset($_SESSION['email']))
// If session is not set then redirect to Login Page
{
  header("Location:login.php");
}

$phone_noErr = $timeslotErr = $locationErr = "";
$phone_no = $timeslot = $location = "";
print_r($_SESSION['email']);
print_r(isset($_SESSION['email']));

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] == $_POST['token']) {

  if (empty($_POST["phone_no"])) {
    $phone_noErr = "Phone No. is required";
  } else {
    $phone_no = test_input($_POST["phone_no"]);
    if (!preg_match("/^[2-9][0-9]{7}$/", $phone_no)) {
      $phone_noErr = "Please input valid Hong Kong Phone No.";
    }
  }

  if (empty($_POST["timeslot"])) {
    $timeslotErr = "Timeslot is required";
  } else {
    $timeslot = test_input($_POST["timeslot"]);
    if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})T((\d{2}):(\d{2}))$/", $timeslot)) {
      $timeslotErr = "Please input valid datetime";
    }
  }
  $centers = array(
    'Hong Kong Island',
    'Kowloon',
    'New Territories'
  );
  if (empty($_POST["location"])) {
    $locationErr = "Location is required";
  } else {
    $location = $_POST["location"];

    if (!in_array($location, $centers)) {
      $locationErr = "Please input valid location";
    } else {
      $locationErr = "";
    }
  }


  if ($phone_noErr == "" && $timeslotErr == "" && $locationErr == "") {

    $search_sql = $connection->prepare("SELECT * FROM applicants where email = ? ");
    $search_sql->bind_param("s", $email);
    $search_sql->execute();
    $search_sql->store_result();


    $update_sql = $connection->prepare("UPDATE applicants SET phone_no=? , timeslot=? , location=? WHERE email = ?");
    $update_sql->bind_param("isss", $phone_no, $timeslot, $location, $email);
    $update_sql->execute();
    echo "<h2>Booking Success!! Your Phone no. is $phone_no. Please go to $location center on " . substr($timeslot, 0, 10) . " at " . substr($timeslot, 11, 5) . "</h2>";
    // }
  }
}
echo "<pre>";
print_r($_POST);
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <div class="full-screen-container">
    <div class="profile-container">
      <div style="display: flex; justify-content: space-between;">
        <h2>Your account: <?php echo $email ?></h2>
        <a href="logout.php"><button type="submit" name="Submit" class="logout-button">Log Out</button></a>
      </div>
    </div>
    <div class="booking-container">
      <h1 class="login-title">Online Appointment Booking for Replacement of ID Cards</h1><br>
      <h1 class="login-title2">Appointment Booking Page</h1>

      <form id="booking" class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <?php if ($phone_noErr == "") {
          echo '<div class="input-group success">';
        } else {
          echo '<div class="input-group error">';
        } ?>
        <label for="phone_no">Phone No.</label>
        <input type="text" name="phone_no" id="email" require>
        <span class="msg"><?php echo $phone_noErr; ?></span>
    </div>
    <br>
    <label for="location">Location</label>
    <div class="select">
      <select name="location" id="location">
        <option selected disabled>Choose a center</option>
        <option value="Hong Kong Island">Hong Kong Island</option>
        <option value="Kowloon">Kowloon</option>
        <option value="New Territories">New Territories</option>
      </select>
    </div>
    <span class="msg2"><?php echo $locationErr; ?></span>

    <?php
    $mindate = date('Y-m-d H:i', strtotime('+1 day'));
    $maxdate = date('Y-m-d H:i', strtotime('+1 month'));
    ?>
    <br>
    <label>Appointment Date (You can make an appointment within 1 month) </label>
    <input type="datetime-local" name="timeslot" min="<?= $mindate ?>" max="<?= $maxdate ?>" id="timeslot">

    <span class="msg2"><?php echo $timeslotErr; ?></span>
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <button type="submit" name="Submit" class="login-button">Submit</button>
    </form>


  </div>
  </div>
  <script>
    function My_DatetimeLocal() {
      document.getElementById("timeslot").step = "15";
    }
  </script>
</body>

</html>