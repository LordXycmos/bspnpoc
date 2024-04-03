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
$type = $_POST['type_snaar'];
$prijs = $_POST['prijs'];



// GENERAL
checkExistence($conn, $type);
createString($conn, $type, $prijs);
header("Location: snaren.php");



// FUNCTIONS
function checkExistence($conn, $item)
{
  $sql = "SELECT * FROM snaren WHERE type=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $item);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    header('location: snaren.php');
    exit();
  }
}

function createString($conn, $type, $prijs)
{
  $stmt = $conn->prepare("INSERT INTO snaren (type, prijs)
      VALUES(?,?)");
  $stmt->bind_param("sd", $type, $prijs);
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