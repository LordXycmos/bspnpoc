<?php
// BASICS
session_start();
include_once 'dbconnection.php';
include_once 'general-include.php';

if (!isset ($_SESSION['id']) && is_numeric($_GET['id'])) {
  header('location:login.php');
}

if ($role['rol'] != "manager") {
  header('location:home.php');
}

if (isset ($_GET['id'])) {
  $id = $_GET['id'];
} else {
  header("Location: medewerkers.php");
}

checkExistence($job_id, $conn);



// VARIABLES
$voornaam = ucfirst(strtolower($_POST['voornaam']));
$achternaam = ucfirst(strtolower($_POST['achternaam']));
$email = $_POST['email'];
$rol = $_POST['rol'];



// GENERAL
editEmployee($conn, $voornaam, $achternaam, $email, $rol, $id);

$conn->close();
header("Location: medewerker-weergeven.php?id=" . $id);
exit();



// FUNCTIONS
function checkExistence($id, $conn)
{
  $sql = "SELECT * FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (empty ($result)) {
    header("Location: medewerkers.php");
  }
}

function editEmployee($conn, $voornaam, $achternaam, $email, $rol, $id)
{
  $stmt = $conn->prepare("UPDATE medewerker SET voornaam=?, achternaam=?, email=?, rol=? WHERE id=?");
  $stmt->bind_param("ssssi", $voornaam, $achternaam, $email, $rol, $id);
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