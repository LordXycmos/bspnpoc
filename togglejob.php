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

if (isset ($_GET['value']) && isset ($_GET['id']) && is_numeric($_GET['value']) && is_numeric($_GET['id'])) {
  $action = $_GET['value'];
  $job_id = $_GET['id'];
} else {
  header("Location: overzicht.php");
}

checkExistence($job_id, $conn);



// GENERAL
if ($action == "deactivate") {
  deactivateJob($job_id, $conn);
} else if ($action == "activate") {
  $status = findStatus($job_id, $conn);
  activateJob($job_id, $status, $conn);
} else if ($action == "delete") {
  deleteJob($job_id, $conn);
}



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

function findStatus($id, $conn)
{
  $sql = "SELECT * FROM job WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $status = $stmt->get_result()->fetch_assoc();

  if (!empty ($status['uitgegeven_door'])) {
    return 3;
  } else if (!empty ($status['bespannen_door'])) {
    return 2;
  } else if (!empty ($status['aangenomen_door'])) {
    return 1;
  } else {
    return 0;
  }
}

function deactivateJob($id, $conn)
{
  $stmt = $conn->prepare("UPDATE job SET status=0 WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: overzicht-weergeven.php?id=" . $id);
}

function activateJob($id, $status, $conn)
{
  $stmt = $conn->prepare("UPDATE job SET status=? WHERE id=?");
  $stmt->bind_param("ii", $status, $id);
  $stmt->execute();
  $stmt->close();
  header("Location: overzicht-weergeven.php?id=" . $id);
}

function deleteJob($id, $conn)
{
  $stmt = $conn->prepare("DELETE FROM job WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: overzicht.php");
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}