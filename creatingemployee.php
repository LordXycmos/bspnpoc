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
$voornaam = ucfirst(strtolower($_POST['voornaam']));
$achternaam = ucfirst(strtolower($_POST['achternaam']));
$email = $_POST['email'];
$password = $_POST['wachtwoord'];
$wachtwoord = password_hash($password, PASSWORD_DEFAULT);
$rol = $_POST['rol'];



// GENERAL
checkExistence($conn, $email);
createEmployee($conn, $voornaam, $achternaam, $email, $wachtwoord, $rol);
header("Location: medewerkers.php");



// FUNCTIONS
function checkExistence($conn, $item)
{
  $sql = "SELECT * FROM medewerker WHERE email=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $item);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (!empty($result)) {
    header('location: medewerkers.php');
    exit();
  }
}

function createEmployee($conn, $voornaam, $achternaam, $email, $wachtwoord, $rol)
{
  $stmt = $conn->prepare("INSERT INTO medewerker (voornaam, achternaam, email, wachtwoord, rol)
      VALUES(?,?,?,?,?)");
  $stmt->bind_param("sssss", $voornaam, $achternaam, $email, $wachtwoord, $rol);
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