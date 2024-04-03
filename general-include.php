<?php
if (!isset ($_SESSION['id'])) {
  header('location:login.php');
}

// VARIABLES
$user = getUser($_SESSION['id'], $conn);
$role = getRole($_SESSION['id'], $conn);
// Desktop
$longname = $user['voornaam'] . " " . $user['achternaam'];
// Phone
$shortname = substr($user['voornaam'], 0, 1) . "." . substr($user['achternaam'], 0, 1) . ".";



// GENERAL
if ($role['rol'] == "bespannen" || $role['rol'] == "allround" || $role['rol'] == "manager") {
  $salarisButton = "";
} else {
  $salarisButton = "style='visibility: hidden'";
}

if ($role['rol'] == "manager") {
  $managerButton = "";
} else {
  $managerButton = "style='visibility: hidden'";
}



// FUNCTIONS
function getUser(int $id, $conn)
{
  $sql = "SELECT * FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getRole(int $id, $conn)
{
  $sql = "SELECT rol FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}