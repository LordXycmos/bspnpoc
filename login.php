<?php
// BASICS
session_start();
include_once 'dbconnection.php';



// VARIABLES
$errormessage = "vul in uw email en wachtwoord";



// GENERAL
if (isset($_POST['submit'])) {
  $email = $_POST["email"];
  $wachtwoord = $_POST["wachtwoord"];

  $result = verifyLogin($email, $conn);

  if (!empty($result)) {
    if (password_verify($wachtwoord, $result['wachtwoord'])) {
      $_SESSION['id'] = $result['id'];
      header("location:home.php");
      die;
    }
  }
  $errormessage = "onjuiste email of wachtwoord";
}

// FUNCTIONS
function verifyLogin($email, $conn)
{
  $sql = "SELECT * FROM medewerker WHERE email=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="custom.css" />
  <link href="https://beessportnl.github.io/static-files/bootstrap-sb-admin.css" rel="stylesheet" />
  <title>Login - BeeS Bespanlijst</title>
</head>

<body class="login-body custom-login">
  <main class="d-flex justify-content-center">
    <div class="d-inline-flex bg-white shadow mt-5 p-5 rounded border">
      <form method="post">
        <div class="d-flex justify-content-center">
          <p class="text-warning">BeeS Bespanlijst</p>
        </div>
        <input class="d-block mb-2 rounded border p-2" name="email" type="email" required placeholder="e-mail" />
        <input class="d-block mb-2 rounded border p-2" name="wachtwoord" required type="password"
          placeholder="wachtwoord" />
        <input class="d-block w-100 btn btn-outline-warning" type="submit" name="submit" value="login" />
        <?php echo "<p><small>" . $errormessage . "</small></p>" ?>
        <div class="d-flex justify-content-center">
          <img src="images/bees-logo.png" class="w-50 mt-5" />
        </div>
      </form>
    </div>
  </main>
</body>

</html>