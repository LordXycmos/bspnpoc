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
$type = ucfirst(strtolower($_POST['type']));



// GENERAL
checkExistence($conn, $type);
createCustomerType($conn, $type);
header("Location: klanten.php");



// FUNCTIONS
function checkExistence($conn, $item)
{
  $sql = "SELECT * FROM klant_type WHERE type=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $item);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    header('location: klanten.php');
    exit();
  }
}

function createCustomerType($conn, $type)
{
  $stmt = $conn->prepare("INSERT INTO klant_type (type)
      VALUES(?)");
  $stmt->bind_param("s", $type);
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