<?php
// BASICS
session_start();
include_once 'dbconnection.php';

if (!isset ($_SESSION['id'])) {
  header('location:login.php');
}

$role = getRole($_SESSION['id'], $conn);
if ($role['rol'] != "manager") {
  header('location:home.php');
}

if (isset ($_GET['id']) && is_numeric($_GET['id'])) {
  $id = $_GET['id'];
} else {
  header("Location: medewerkers.php");
}

checkExistence($id, $conn);



// GENERAL
deleteEmployee($id, $conn);



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

function deleteEmployee($id, $conn)
{
  $stmt = $conn->prepare("DELETE FROM medewerker WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: medewerkers.php");
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}