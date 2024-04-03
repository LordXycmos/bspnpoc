<?php
// BASICS
session_start();
include_once 'dbconnection.php';

if (!isset ($_SESSION['id']) && is_numeric($_GET['id'])) {
  header('location:login.php');
}

if (isset ($_GET['id'])) {
  $job_id = $_GET['id'];
} else {
  header("Location: overzicht.php");
}

checkExistence($job_id, $conn);



// VARIABLES
$job = getJob($conn, $job_id);
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



// GENERAL
if ($job['status'] >= 2) {
  $gebroken_snaren = $_POST['gebroken_snaren'];
  $racket_breuk = $_POST['racket_breuk'];
  $apparaat = $_POST['apparaat'];
  $uitbetalings_methode = $_POST['uitbetaal_methode'];
  $bespannen_door = $_POST['bespanner'];
}
if ($job['status'] >= 3) {
  $pin = $_POST['pin'];
  $bedrag = $_POST['bedrag'];
  $factuur = $_POST['factuur'];
  $uitgever = $_POST['uitgever'];
}

if ($job['status'] == 1) {
  editJobOne($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $status, $job_id);
} else if ($job['status'] == 2) {
  editJobTwo($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $job_id);
} else if ($job['status'] >= 3) {
  editJobThree($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $pin, $bedrag, $factuur, $uitgever, $job_id);
}

$conn->close();
header("Location: overzicht-weergeven.php?id=" . $job_id);
exit();



// FUNCTIONS
function getJob($conn, $id)
{
  $sql = "SELECT * FROM job WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getCustomerId($conn, $voornaam, $achternaam, $email, $klant_type, $aanmaak_datum)
{
  $sql = "SELECT id FROM klant WHERE voornaam=? AND achternaam=? AND email=? AND klant_type=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $voornaam, $achternaam, $email, $klant_type);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty ($result)) {
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

function editJobOne($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $status, $id)
{
  if ($conn->connect_error) {
    die ('Connection Failed : ' . $conn->connect_error);
  } else {
    $stmt = $conn->prepare("UPDATE job SET volgnummer=?, klant=?, racket_type=?, snaar=?, kracht_lengte=?, kracht_breedte=?, spoed=?, aangenomen_door=?, status=? WHERE id=?");
    $stmt->bind_param("iisiiiiiii", $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $status, $id);
    $stmt->execute();
    $stmt->close();
  }
}

function editJobTwo($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $id)
{
  if ($conn->connect_error) {
    die ('Connection Failed : ' . $conn->connect_error);
  } else {
    $stmt = $conn->prepare("UPDATE job SET volgnummer=?, klant=?, racket_type=?, snaar=?, kracht_lengte=?, kracht_breedte=?, spoed=?, aangenomen_door=?, gebroken_snaren=?, racket_breuk=?, apparaat=?, uitbetalings_methode=?, bespannen_door=? WHERE id=?");
    $stmt->bind_param("iisiiiiiiiiiii", $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $id);
    $stmt->execute();
    $stmt->close();
  }
}

function editJobThree($conn, $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $pin, $bedrag, $factuur, $uitgever, $id)
{
  if ($conn->connect_error) {
    die ('Connection Failed : ' . $conn->connect_error);
  } else {
    $stmt = $conn->prepare("UPDATE job SET volgnummer=?, klant=?, racket_type=?, snaar=?, kracht_lengte=?, kracht_breedte=?, spoed=?, aangenomen_door=?, gebroken_snaren=?, racket_breuk=?, apparaat=?, uitbetalings_methode=?, bespannen_door=?, pin=?, bedrag=?, factuur_nummer=?, uitgegeven_door=? WHERE id=?");
    $stmt->bind_param("iisiiiiiiiiiiiiiii", $volgnummer, $klant, $rackettype, $snaartype, $kracht_lengte, $kracht_breedte, $spoed, $aannemer, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $pin, $bedrag, $factuur, $uitgever, $id);
    $stmt->execute();
    $stmt->close();
  }
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function checkExistence($id, $conn)
{
  $sql = "SELECT * FROM job WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (empty ($result)) {
    header("Location: overzicht.php");
  }
}