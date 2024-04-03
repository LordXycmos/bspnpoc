<?php
// BASICS
session_start();
include_once 'dbconnection.php';

if (!isset ($_SESSION['id'])) {
  header('location:login.php');
}

if (isset ($_GET['id'])) {
  $job_id = $_GET['id'];
} else {
  header("Location: overzicht.php");
}

checkExistence($job_id, $conn);

// VARIABLES
$jobStatus = getJobStatus($conn, $job_id);



// FUNCTIONS
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


if ($jobStatus['status'] == 1) {
  $gebroken_snaren = $_POST['gebroken_snaren'];
  $racket_breuk = $_POST['racket_breuk'];
  $apparaat = $_POST['apparaat'];
  $uitbetalings_methode = $_POST['uitbetaal_methode'];
  $bespannen_door = $_POST['bespanner'];

  useMachine($conn, $apparaat);
  bespannenJob($conn, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $job_id);
  $conn->close();
  header("Location: overzicht-weergeven.php?id=" . $job_id);
  exit();
} else if ($jobStatus['status'] == 2) {
  $pin = $_POST['pin'];
  $bedrag = $_POST['bedrag'];
  $factuur = $_POST['factuur'];
  $uitgever = $_POST['uitgever'];

  uitgevenJob($conn, $pin, $bedrag, $factuur, $uitgever, $job_id);

  $conn->close();
  header("Location: overzicht-weergeven.php?id=" . $job_id);
  exit();
} else if ($jobStatus['status'] == 3) {
  $uitbetaald = $_POST['uitbetaald'];
  if ($uitbetaald == 1) {
    uitbetaaldJob($conn, $job_id);
  }

  $conn->close();
  header("Location: overzicht-weergeven.php?id=" . $job_id);
  exit();
}

function getJobStatus($conn, $id)
{
  $sql = "SELECT status FROM job WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function bespannenJob($conn, $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $id)
{
  if ($conn->connect_error) {
    die ('Connection Failed : ' . $conn->connect_error);
  } else {
    $stmt = $conn->prepare("UPDATE job SET gebroken_snaren=?, racket_breuk=?, apparaat=?, uitbetalings_methode=?, bespannen_door=?, status=2 WHERE id=?");
    $stmt->bind_param("iiiiii", $gebroken_snaren, $racket_breuk, $apparaat, $uitbetalings_methode, $bespannen_door, $id);
    $stmt->execute();
    $stmt->close();
  }
}

function uitgevenJob($conn, $pin, $bedrag, $factuur, $uitgegeven_door, $id)
{
  if ($conn->connect_error) {
    die ('Connection Failed : ' . $conn->connect_error);
  } else {
    $stmt = $conn->prepare("UPDATE job SET pin=?, bedrag=?, factuur_nummer=?, uitgegeven_door=?, status=3 WHERE id=?");
    $stmt->bind_param("iiiii", $pin, $bedrag, $factuur, $uitgegeven_door, $id);
    $stmt->execute();
    $stmt->close();
  }
}

function uitbetaaldJob($conn, $id)
{
  $month = date('m');
  $stmt = $conn->prepare("UPDATE job SET status=4, uitbetaal_maand=? WHERE id=?");
  $stmt->bind_param("ii", $month, $id);
  $stmt->execute();
  $stmt->close();
}

function useMachine($conn, $id)
{
  $sql = "SELECT * FROM apparaten WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $machine = $stmt->get_result()->fetch_assoc();

  $number = $machine['keren_gebruikt'] + 1;

  $stmt = $conn->prepare("UPDATE apparaten SET keren_gebruikt=? WHERE id=?");
  $stmt->bind_param("ii", $number, $id);
  $stmt->execute();
  $stmt->close();
  $id = $conn->insert_id;
}