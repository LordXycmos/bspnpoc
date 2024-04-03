<?php
// BASICS
session_start();
include_once 'dbconnection.php';

if (!isset($_SESSION['id']) && is_numeric($_GET['id'])) {
  header('location:login.php');
}

$role = getRole($_SESSION['id'], $conn);
if ($role['rol'] != "manager" || $role['rol'] != "kassa" || $role['rol'] != "allround") {
  header('location:home.php');
}



// VARIABLES
$voornaam = ucfirst(strtolower($_POST['voornaam']));
$achternaam = ucfirst(strtolower($_POST['achternaam']));
$email = strtolower($_POST['email']);
$klant_type = $_POST['opdrachtgever'];
$volgnummer = $_POST['volgnummer'];
$status = 1;
$rackettype = $_POST['rackettype'];
$snaartype = $_POST['snaartype'];
$kracht_lengte = $_POST['kracht_lengte'];
$kracht_breedte = $_POST['kracht_breedte'];
$spoed = $_POST['spoed'];
$aannemer = $_POST['aannemer'];
$aanneemdatum = date('Y-m-d H:i:s');
$klant = getCustomerId($conn, $voornaam, $achternaam, $email, $klant_type, $aanneemdatum);
$id = createJob($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $aanneemdatum, $status);



// GENERAL
$conn->close();
header("Location: overzicht-weergeven.php?id=" . $id);
exit();



// FUNCTIONS
function getCustomerId($conn, $voornaam, $achternaam, $email, $klant_type, $aanmaak_datum)
{
  $sql = "SELECT id FROM klant WHERE voornaam=? AND achternaam=? AND email=? AND klant_type=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $voornaam, $achternaam, $email, $klant_type);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    var_dump($result['id']);
    return $result['id'];
  } else {
    $stmt = $conn->prepare("INSERT INTO klant (voornaam, achternaam, email, klant_type, aanmaak_datum)
      VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssis", $voornaam, $achternaam, $email, $klant_type, $aanmaak_datum);
    $stmt->execute();
    $stmt->close();
    $klant_id = $conn->insert_id;
    return $klant_id;
  }

}

function createJob($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $aanneemdatum, $status)
{
  if ($conn->connect_error) {
    die('Connection Failed : ' . $conn->connect_error);
  } else {
    try {
      $stmt = $conn->prepare("INSERT INTO job (volgnummer, klant, racket_type, snaar, kracht_lengte, kracht_breedte, spoed, aangenomen_door, aanneem_datum, status)
      VALUES(?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param("iisiiiiisi", $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $aanneemdatum, $status);
      $stmt->execute();
      $stmt->close();
      $id = $conn->insert_id;
    } catch (\Throwable $th) {
      var_dump($th);
    }

  }
  return $id;
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}