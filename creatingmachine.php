<?php
// BASICS
session_start();
include_once 'dbconnection.php';

if (!isset($_SESSION['id']) && is_numeric($_GET['id'])) {
  header('location:login.php');
}

$role = getRole($_SESSION['id'], $conn);
if ($role['rol'] != "manager") {
  header('location:home.php');
}



// VARIABLES
$naam = $_POST['naam'];



// GENERAL
checkExistence($conn, $naam);
createMachine($conn, $naam);
header("Location: apparaten.php");



// FUNCTIONS
function checkExistence($conn, $item)
{
  $sql = "SELECT * FROM apparaten WHERE naam=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $item);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    header('location: apparaten.php');
    exit();
  }
}

function createMachine($conn, $naam)
{
  $stmt = $conn->prepare("INSERT INTO apparaten (naam)
      VALUES(?)");
  $stmt->bind_param("s", $naam);
  $stmt->execute();
  $stmt->close();
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}