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
$bespan_kracht = $_POST['bespan_kracht'];



// GENERAL
checkExistence($conn, $bespan_kracht);
createString($conn, $bespan_kracht);
header("Location: snaren.php");



// FUNCTIONS
function checkExistence($conn, $item)
{
  $sql = "SELECT * FROM bespan_kracht WHERE bespankracht=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $item);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    header('location: snaren.php');
    exit();
  }
}

function createString($conn, $bespankracht)
{
  $stmt = $conn->prepare("INSERT INTO bespan_kracht (bespankracht)
      VALUES(?)");
  $stmt->bind_param("d", $bespankracht);
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