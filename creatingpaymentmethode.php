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
$prijs = $_POST['prijs'];



// GENERAL
createString($conn, $type, $prijs);
header("Location: uitbetalen.php");



// FUNCTIONS
function createString($conn, $type, $prijs)
{
  $stmt = $conn->prepare("INSERT INTO uitbetalings_methode (type, geld)
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