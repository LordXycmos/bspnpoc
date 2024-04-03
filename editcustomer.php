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
  header("Location: klanten.php");
}

checkExistence($job_id, $conn);


// VARIALBES
$voornaam = ucfirst(strtolower($_POST['voornaam']));
$achternaam = ucfirst(strtolower($_POST['achternaam']));
$email = $_POST['email'];
$klant_type = $_POST['klant_type'];


// GENERAL
editEmployee($conn, $voornaam, $achternaam, $email, $klant_type, $id);

$conn->close();
header("Location: klant-weergeven.php?id=" . $id);
exit();



// FUNCTIONS
function checkExistence($id, $conn)
{
  $sql = "SELECT * FROM klant WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  if (empty ($result)) {
    header("Location: klanten.php");
  }
}

function editEmployee($conn, $voornaam, $achternaam, $email, $klant_type, $id)
{
  $stmt = $conn->prepare("UPDATE klant SET voornaam=?, achternaam=?, email=?, klant_type=? WHERE id=?");
  $stmt->bind_param("sssii", $voornaam, $achternaam, $email, $klant_type, $id);
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