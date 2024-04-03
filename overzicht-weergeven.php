<?php
// BASICS
session_start();
include_once 'dbconnection.php';
include_once 'general-include.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = $_GET['id'];
} else {
  header("Location: overzicht.php");
}

$job = getJob($id, $conn);
if (empty($job)) {
  header("Location: overzicht.php");
}

if ($job['status'] >= 4 || $job['status'] == 0) {
  if ($role['rol'] != "manager") {
    header('location:overzicht.php');
  }
}

// VARIABLES
$snaartype = getStrings($conn);
$bespankracht = getStrengths($conn);
$opdrachtgever = getCustomerTypes($conn);
$klant = getCustomer($job['klant'], $conn);
$klantType = getCustomerType($klant['klant_type'], $conn);
$aangenomen_door = getEmployee($job['aangenomen_door'], $conn);
$snaar = getString($job['snaar'], $conn);
$kracht_lengte = getStrength($job['kracht_lengte'], $conn);
$kracht_breedte = getStrength($job['kracht_breedte'], $conn);
$bespannen_door = "-";
$apparaat = "-";
$gebroken_snaren = "-";
$racket_breuk = "-";
$uitbetalings_methode = "-";
$pin = "-";
$factuur = "-";
$bedrag = "-";
$uitgegeven_door = "-";
$uitbetaald = "-";
$bespanner = getEmployees($conn, "bespannen");
$aannemer = getEmployees($conn, "kassa");
$uitgever = getEmployees($conn, "kassa");
$aannemer = getKassaEmployees($conn);
$apparaten = getMachines($conn);
$uitbetalings_methodes = getPaymentMethode($conn);

$spoed = array(
  (object) [
    'id' => '0',
    'bool' => 'Nee'
  ],
  (object) [
    'id' => '1',
    'bool' => 'Ja'
  ]
);



// GENERAL
if ($role['rol'] == "bespannen" && $job['status'] < 2 || $role['rol'] == "allround" || $role['rol'] == "manager") {
  if ($job['status'] == 3 && $role['rol'] == 'manager' || $job['status'] < 3) {
    $klaarButton = "<p id='btn-three' class='custom-9 nav-unselected m-0 d-flex justify-content-center text-center py-1 text-dark'>klaar</p>";
    $klaarBtnActive = "pages.push('PageThree');";
  } else {
    $klaarButton = "<p id='btn-three'></p>";
    $klaarBtnActive = "";
  }
} else {
  $klaarButton = "<p id='btn-three'></p>";
  $klaarBtnActive = "";
}

if ($job['status'] >= 2) {
  if (!empty($job['bespannen_door'])) {
    $bespannen_door = getEmployee($job['bespannen_door'], $conn);
  }
  if (!empty($job['apparaat'])) {
    $apparaat = getMachine($job['apparaat'], $conn);
  }
  if (!empty($job['gebroken_snaren'])) {
    $gebroken_snaren = $job['gebroken_snaren'];
  } else {
    $gebroken_snaren = "0";
  }
  if (!empty($job['racket_breuk'])) {
    $racket_breuk = jaNee($job['racket_breuk']);
  } else {
    $racket_breuk = "Nee";
  }
  if (!empty($job['uitbetalings_methode'])) {
    $uitbetalings_methode = getPayment($job['uitbetalings_methode'], $conn);
  }
}

if ($job['status'] >= 3) {
  if (!empty($job['pin'])) {
    $pin = jaNee($job['bespannen_door']);
  } else {
    $pin = "Nee";
  }
  if (!empty($job['factuur_nummer'])) {
    $factuur = $job['apparaat'];
  } else {
    $factuur = "-";
  }
  $bedrag = "€" . $job['bedrag'];
  if (!empty($job['uitgegeven_door'])) {
    $uitgegeven_door_employee = getEmployee($job['uitgegeven_door'], $conn);
    $uitgegeven_door = $uitgegeven_door_employee['voornaam'] . " " . $uitgegeven_door_employee['achternaam'];
  }
}

if ($job['status'] >= 4) {
  $uitbetaald = "Ja";
}

if ($job['status'] == 0) {
  $actionBtn = "<a href='togglejob.php?value=activate&id=" . $id . "' class='d-flex mt-3 btn btn-outline-dark phone-mx-1 justify-content-center'>activeren</a>" . "<a href='togglejob.php?value=delete&id=" . $id . "' class='d-flex mt-3 btn btn-outline-dark phone-mx-1 justify-content-center'>verwijderen</a>";
} else {
  $actionBtn = "<a href='togglejob.php?value=deactivate&id=" . $id . "' class='d-flex mt-3 btn btn-outline-dark phone-mx-1 justify-content-center'>verwijderen</a>";
}



// FUNCTIONS
function getCustomerTypes($conn)
{
  $sql = "SELECT * FROM klant_type";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStrengths($conn)
{
  $sql = "SELECT * FROM bespan_kracht";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStrings($conn)
{
  $sql = "SELECT * FROM snaren";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function jaNee($var)
{
  if ($var == 0) {
    return "Nee";
  } else {
    return "Ja";
  }
}

function getJob(int $id, $conn)
{
  $sql = "SELECT * FROM job WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getCustomer(int $id, $conn)
{
  $sql = "SELECT * FROM klant WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getPayment(int $id, $conn)
{
  $sql = "SELECT * FROM uitbetalings_methode WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getCustomerType(int $id, $conn)
{
  $sql = "SELECT * FROM klant_type WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getEmployee(int $id, $conn)
{
  $sql = "SELECT * FROM medewerker WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getString(int $id, $conn)
{
  $sql = "SELECT * FROM snaren WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getStrength(int $id, $conn)
{
  $sql = "SELECT * FROM bespan_kracht WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getKassaEmployees($conn)
{
  $sql = "SELECT * FROM medewerker WHERE rol='kassa' OR rol='allround' OR rol='manager'";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getEmployees($conn, $rol)
{
  $sql = "SELECT * FROM medewerker WHERE rol=? OR rol='allround' OR rol='manager'";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $rol);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getMachines($conn)
{
  $sql = "SELECT * FROM apparaten";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPaymentMethode($conn)
{
  $sql = "SELECT * FROM uitbetalings_methode";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getMachine(int $id, $conn)
{
  $sql = "SELECT * FROM apparaten WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="custom.css" />
  <link href="https://beessportnl.github.io/static-files/bootstrap-sb-admin.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <title>Weergeven - BeeS Bespanlijst</title>
</head>

<body>
  <header
    class="custom-1 position-fixed w-100 d-custom-flex justify-content-between align-items-center text-dark phone-none">
    <p class="m-0 h5 py-3">BeeS Bespanlijst</p>
    <div class="custom-2 header-profile d-flex justify-content-between align-items-center">
      <p class="m-0">
        <?php echo $longname; ?>
      </p>
      <p class="m-0">|</p>
      <span id="header-pfp" class="custom-3 material-icons rounded-circle">account_circle</span>
    </div>
  </header>
  <div
    class="m-2 shadow-l rounded d-custom-none phone-flex justify-content-between align-items-center mb-5 shadow border p-1"
    id="phone-nav">
    <span id="expand-main-nav" class="custom-13 p-1 rounded material-icons text-white bg-gradient-primary">list</span>
    <p class="mb-0 mr-3"><strong>
        <?php echo $shortname; ?>
      </strong></p>
  </div>
  <nav class="custom-4 bg-gradient-primary text-center phone-none" id="main-nav-bar">
    <!-- desktop -->
    <div class="custom-5 d-custom-flex justify-center text-center align-items-center p-3 phone-none" id="nav-expand">
      <div id="nav-one" class="text-white d-custom-none">inklappen</div>
      <span class="custom-6 material-icons nav-collapse-btn text-white">list</span>
    </div>
    <!-- phone -->
    <div class="custom-5 justify-content-between text-center align-items-center p-3 d-custom-none phone-flex mb-5"
      id="collapse-main-nav">
      <div id="nav-zero" class="text-white">inklappen</div>
      <span class="custom-6 material-icons nav-collapse-btn text-white">list</span>
    </div>
    <!-- - -->
    <a href="home.php"
      class="custom-5 d-flex text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-home">
      <div id="nav-two" class=" d-custom-none phone-block" style="color: #8e8e8e">home</div>
      <span class="custom-6 material-icons">home</span>
    </a>
    <a href="overzicht.php"
      class="custom-5 d-flex text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-overzicht">
      <div id="nav-three" class=" d-custom-none phone-block" style="color: #8e8e8e">overzicht</div>
      <span class="custom-6 material-icons">dns</span>
    </a>
    <a href="salaris.php"
      class="custom-5 d-flex text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-salaris" <?php echo $salarisButton; ?>>
      <div id="nav-four" class=" d-custom-none phone-block" style="color: #8e8e8e">salaris</div>
      <span class="custom-6 material-icons">payments</span>
    </a>
    <a href="rapportages.php"
      class="custom-5 d-custom-flex text-decoration-none justify-center text-center align-items-center p-3 phone-none"
      id="nav-manager" <?php echo $managerButton; ?>>
      <div id="nav-five" class=" d-custom-none phone-block" style="color: #8e8e8e">manager</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="rapportages.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-rapportages" <?php echo $managerButton; ?>>
      <div id="nav-six" class=" d-custom-none phone-block" style="color: #8e8e8e">rapportages</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="uitbetalen.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-uitbetalen" <?php echo $managerButton; ?>>
      <div id="nav-seven" class=" d-custom-none phone-block" style="color: #8e8e8e">uitbetalen</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="medewerkers.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-medewerkers" <?php echo $managerButton; ?>>
      <div id="nav-eight" class=" d-custom-none phone-block" style="color: #8e8e8e">medewerkers</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="klanten.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-klanten" <?php echo $managerButton; ?>>
      <div id="nav-nine" class=" d-custom-none phone-block" style="color: #8e8e8e">klanten</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="snaren.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-snaren" <?php echo $managerButton; ?>>
      <div id="nav-ten" class=" d-custom-none phone-block" style="color: #8e8e8e">snaren</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <a href="apparaten.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-apparaten" <?php echo $managerButton; ?>>
      <div id="nav-eleven" class=" d-custom-none phone-block" style="color: #8e8e8e">apparaten</div>
      <span class="custom-6 material-icons">manage_accounts</span>
    </a>
    <div class="custom-16 d-custom-none phone-flex border-top">
      <a href="profiel.php"
        class="custom-5 text-decoration-none justify-content-center text-center align-items-center p-3 d-flex w-50">
        <span class="custom-6 material-icons">account_circle</span>
      </a>
      <a href="logout.php"
        class="custom-5 text-decoration-none justify-content-center text-center align-items-center p-3 d-flex w-50">
        <span class="custom-6 material-icons">logout</span>
      </a>
    </div>
  </nav>
  <div>
    <p class="invisible">1</p>
  </div>

  <div class="custom-7 position-fixed d-none justify-content-between px-2 p-1 mt-4 bg-white phone-none"
    id="dropdown-nav">
    <a href="profiel.php" class="text-muted mr-2 text-decoration-none h-auto" style="height: 24px;">
      <span class="custom-15 material-icons">account_circle</span>
    </a>
    <a href="logout.php" class="text-warning text-decoration-none h-auto" style="height: 24px;">
      <span class="custom-15 material-icons">logout</span>
    </a>
  </div>
  <main id="main" class="">
    <div class="custom-8 d-custom-flex phone-none justify-content-between">
      <div class="d-flex">
        <p id="btn-one" class="custom-9 nav-selected m-0 d-flex justify-content-center text-center py-1 text-dark">info
        </p>
        <p id="btn-two" class="custom-9 nav-unselected m-0 d-flex justify-content-center text-center py-1 text-dark">
          wijzig
        </p>
        <?php echo $klaarButton; ?>
      </div>
    </div>
    <div class="d-custom-none phone-flex justify-content-between mx-5 p-0 align-items-center mb-3">
      <p id="previous-content-page"
        class="custom-12 d-flex justify-content-center align-items-center px-3 mb-0 text-dark">&#8249;</p>
      <p id="name-content-page" class="d-flex justify-content-center align-items-center mb-0">one</p>
      <p id="next-content-page" class="custom-12 d-flex justify-content-center align-items-center px-3 mb-0 text-dark">
        &#8250;</p>
    </div>
    <div class="custom-10 bg-white pb-3">
      <p id="changeable-content" class="m-0">0</p>
    </div>
  </main>
  <footer id="footer" class="custom-11 w-100 mt-5">
    <p class="invisible">1</p>
  </footer>
</body>

</html>

<script>
  // VARIABLES
  var selectedPage = "btn-one";
  var pages = ["PageOne", "PageTwo"];
  <?php echo $klaarBtnActive; ?>
  var currentPage = 0;



  // GENERAL
  id("nav-expand").addEventListener("click", function () {
    id("main-nav-bar").classList.toggle("w-custom-15");
    expandNavigationBar("nav-expand", "nav-one");
    expandNavigationBar("nav-home", "nav-two");
    expandNavigationBar("nav-overzicht", "nav-three");
    expandNavigationBar("nav-salaris", "nav-four");
    expandNavigationBar("nav-manager", "nav-five");
  });

  id("header-pfp").addEventListener("click", function () {
    id("dropdown-nav").classList.toggle("d-custom-flex");
    id("dropdown-nav").classList.toggle("d-none");
  });

  id("expand-main-nav").addEventListener("click", function () {
    id("footer").classList.add("phone-none");
    id("main").classList.add("phone-none");
    id("phone-nav").classList.add("d-none");
    id("main-nav-bar").classList.remove("phone-none");
  });

  id("collapse-main-nav").addEventListener("click", function () {
    id("footer").classList.remove("phone-none");
    id("main").classList.remove("phone-none");
    id("phone-nav").classList.remove("d-none");
    id("main-nav-bar").classList.add("phone-none");
  });

  id("btn-one").addEventListener("click", function () {
    currentPage = 0;
    selectPage();
  });

  id("btn-two").addEventListener("click", function () {
    currentPage = 1;
    selectPage();
  });

  id("btn-three").addEventListener("click", function () {
    currentPage = 2;
    selectPage();
  });

  selectPage();

  id("next-content-page").addEventListener("click", function () {
    if (currentPage < pages.length - 1) {
      currentPage++;
      selectPage()
    };
  });

  id("previous-content-page").addEventListener("click", function () {
    if (currentPage > 0) {
      currentPage--;
      selectPage()
    };
  });



  // FUNCTIONS
  function id(id) {
    return document.getElementById(id);
  }

  function expandNavigationBar(btn, text) {
    id(btn).classList.toggle("justify-center");
    id(btn).classList.toggle("justify-between");
    id(text).classList.toggle("d-custom-none");
  }

  function unselectSelector() {
    id(selectedPage).classList.remove("nav-selected");
    id(selectedPage).classList.add("nav-unselected");
  }
  function selectSelector() {
    id(selectedPage).classList.remove("nav-unselected");
    id(selectedPage).classList.add("nav-selected");
  }

  function selectPage() {
    rowStart = "<div class='custom-18 d-custom-flex phone-block'>";
    rowEnd = "</div>";
    var start = "<div class='d-custom-flex phone-block mt-2'>";
    var end = "</div>";
    const job = <?php echo json_encode($job); ?>;
    const aangenomen_door = <?php echo json_encode($aangenomen_door); ?>;
    const klant = <?php echo json_encode($klant); ?>;
    const klantType = <?php echo json_encode($klantType); ?>;
    const snaar = <?php echo json_encode($snaar); ?>;
    const kracht_lengte = <?php echo json_encode($kracht_lengte); ?>;
    const kracht_breedte = <?php echo json_encode($kracht_breedte); ?>;
    const bespannen_door = <?php echo json_encode($bespannen_door); ?>;
    const apparaat = <?php echo json_encode($apparaat); ?>;
    const gebroken_snaren = <?php echo json_encode($gebroken_snaren); ?>;
    const racket_breuk = <?php echo json_encode($racket_breuk); ?>;
    const uitbetalings_methode = <?php echo json_encode($uitbetalings_methode); ?>;
    const pin = <?php echo json_encode($pin); ?>;
    const factuur = <?php echo json_encode($factuur); ?>;
    const bedrag = <?php echo json_encode($bedrag); ?>;
    const uitgever = <?php echo json_encode($uitgegeven_door); ?>;
    const uitbetaald = <?php echo json_encode($uitbetaald); ?>;
    aannemer = aangenomen_door["voornaam"] + " " + aangenomen_door["achternaam"];
    klantnaam = klant["voornaam"] + " " + klant["achternaam"];
    bespanner = checkResult(bespannen_door, bespannen_door["voornaam"] + " " + bespannen_door["achternaam"]);
    uitbetaal_methode = checkResult(uitbetalings_methode, "€" + uitbetalings_methode['geld'] + " " + uitbetalings_methode["type"]);
    names = ["naam klant", "volgnummer", "type klant", "e-mail", "aangenomen door", "spoed", "type snaar", "type racket", "bespankracht lengte", "bespankracht breedte", "bespannen door", "apparaat", "gebroken snaren", "racketbreuk", "uitbetalings methode", "PIN", "factuurnummer", "betaalde bedrag", "uitgegeven door", "uitbetaald"];
    if (job['spoed'] === 0) {
      spoed = "nee";
    } else {
      spoed = "ja";
    }
    stats = [klantnaam, resultCheck(job["volgnummer"]), klantType["type"], klant["email"], aannemer, spoed, snaar['type'], job['racket_type'], resultCheck(kracht_lengte['bespankracht']), resultCheck(kracht_breedte['bespankracht']), bespanner, resultCheck(apparaat['naam']), gebroken_snaren, racket_breuk, uitbetaal_methode, pin, factuur, bedrag, uitgever, uitbetaald];

    if (pages[currentPage] == "PageOne") {
      contentLoop(names, stats);
      pageTitle = "info";
      curPage = "btn-one";
      pageDisplay(content, pageTitle, curPage);
    } else if (pages[currentPage] == "PageTwo") {
      const opdrachtgever = <?php echo json_encode($opdrachtgever); ?>;
      const bespankracht = <?php echo json_encode($bespankracht); ?>;
      const aannemer = <?php echo json_encode($aannemer); ?>;
      const snaartype = <?php echo json_encode($snaartype); ?>;
      const spoed = <?php echo json_encode($spoed); ?>;
      content = "<form action='editjob.php?id=" + <?php echo $id ?> + "' method='post'><div class='content-container w-100 d-flex justify-content-center mt-3'><div class='phone-width-100 mx-1'>";
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "volgnummer" + "</label><input class='form-input-small phone-width-100' type='number' min='0' max='999' required name='" + "volgnummer" + "'/ value='" + job["volgnummer"] + "'></div>";
      content += "<div><label class='d-block mx-1 mb-0'>" + "opdrachtgever" + "</label><select class='form-input-small phone-width-100' name='" + "opdrachtgever" + "'>";
      for (var i = 0; i < opdrachtgever.length; i++) {
        if (opdrachtgever[i].id == klantType['id']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + " value='" + opdrachtgever[i].id + "'>" + opdrachtgever[i].type + "</option>";
      }
      content += "</select></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "e-mail" + "</label><input max-length='100' minlength='3' class='form-input-big phone-width-100' type='email' required name='" + "email" + "' value='" + klant["email"] + "'/></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "type racket" + "</label><input maxlength='100' class='form-input-big phone-width-100' type='text' required name='" + "rackettype" + "' value='" + job['racket_type'] + "'/></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "voornaam" + "</label><input maxlength='50' class='form-input-small phone-width-100' required type='text' name='" + "voornaam" + "' value='" + klant["voornaam"] + "'/></div>";
      content += "<div><label class='d-block mx-1 mb-0'>" + "achternaam" + "</label><input maxlength='100' class='form-input-small phone-width-100' required type='text' name='" + "achternaam" + "' value='" + klant["achternaam"] + "'/></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "type snaar" + "</label><select class='form-input-big phone-width-100' required name='" + "snaartype" + "'>";
      for (var i = 0; i < snaartype.length; i++) {
        if (snaartype[i].id == snaar['id']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + "  value='" + snaartype[i].id + "'>" + snaartype[i].type + "</option>";
      }
      content += "</select></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "bespankracht lengte" + "</label><select class='form-input-small phone-width-100' required name='" + "kracht_lengte" + "'>";
      bespankracht.sort(function (a, b) {
        return a.bespankracht - b.bespankracht;
      });
      for (var i = 0; i < bespankracht.length; i++) {
        if (bespankracht[i].id == kracht_lengte['id']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + "  value='" + bespankracht[i].id + "'>" + bespankracht[i].bespankracht + "</option>";
      }
      content += "</select></div>";
      content += "<div><label class='d-block mx-1 mb-0'>" + "bespankracht breedte" + "</label><select class='form-input-small phone-width-100' required name='" + "kracht_breedte" + "'>";
      for (var i = 0; i < bespankracht.length; i++) {
        if (bespankracht[i].id == kracht_breedte['id']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + "  value='" + bespankracht[i].id + "'>" + bespankracht[i].bespankracht + "</option>";
      }
      content += "</select></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "spoed" + "</label><select class='form-input-small phone-width-100' required name='" + "spoed" + "'>";
      for (var i = 0; i < spoed.length; i++) {
        if (spoed[i].id == job['spoed']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + " value='" + spoed[i].id + "'>" + spoed[i].bool + "</option>";
      }
      content += "</select></div>";
      content += "<div><label class='d-block mx-1 mb-0'>" + "prijs pinnen" + "</label><input class='form-input-small phone-width-100' type='number' min='0' step='0.01' name='" + "prijs" + "'/></div>";
      content += end;
      content += start;
      content += "<div><label class='d-block mx-1 mb-0'>" + "aannemer" + "</label><select class='form-input-big phone-width-100' required name='" + "aannemer" + "'>";
      for (var i = 0; i < aannemer.length; i++) {
        if (aannemer[i].id == aangenomen_door['id']) {
          selected = " selected ";
        } else {
          selected = "";
        }
        content += "<option" + selected + " value='" + aannemer[i].id + "'>" + aannemer[i].voornaam + " " + aannemer[i].achternaam + "</option>";
      }
      content += "</select></div>";
      content += end;
      if (job.status >= 2) {
        const apparaten = <?php echo json_encode($apparaten); ?>;
        const uitbetalings_methode = <?php echo json_encode($uitbetalings_methodes); ?>;
        const bespanner = <?php echo json_encode($bespanner); ?>;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "gebroken snaren" + "</label><select class='form-input-small phone-width-100' required name='" + "gebroken_snaren" + "'>";
        for (var i = 0; i < 6; i++) {
          if (i == job['gebroken_snaren']) {
            selected = " selected ";
          } else {
            selected = "";
          }
          content += "<option" + selected + " value='" + i + "'>" + i + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "racket breuk" + "</label><select class='form-input-small phone-width-100' required name='" + "racket_breuk" + "'><option value='0'>nee</option><option value='1'>ja</option></select></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "apparaat" + "</label><select class='form-input-small phone-width-100' required name='" + "apparaat" + "'>";
        for (var i = 0; i < apparaten.length; i++) {
          if (apparaten[i].id == job['apparaat']) {
            selected = " selected ";
          } else {
            selected = "";
          }
          content += "<option" + selected + " value='" + apparaten[i].id + "'>" + apparaten[i].naam + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "uitbetaal methode" + "</label><select class='form-input-small phone-width-100' required name='" + "uitbetaal_methode" + "'>";
        for (var i = 0; i < uitbetalings_methode.length; i++) {
          if (uitbetalings_methode[i].id == job['uitbetalings_methode']) {
            selected = " selected ";
          } else {
            selected = "";
          }
          content += "<option" + selected + " value='" + uitbetalings_methode[i].id + "'>" + "€" + uitbetalings_methode[i].geld + " " + uitbetalings_methode[i].type + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "bespanner" + "</label><select class='form-input-big phone-width-100' required name='" + "bespanner" + "'>";
        for (var i = 0; i < bespanner.length; i++) {
          if (bespanner[i].id == job['bespannen_door']) {
            selected = " selected ";
          } else {
            selected = "";
          }
          content += "<option" + selected + " value='" + bespanner[i].id + "'>" + bespanner[i].voornaam + " " + bespanner[i].achternaam + "</option>";
        }
        content += "</select></div>";
        content += end;
      }
      if (job.status >= 3) {
        const uitgever = <?php echo json_encode($uitgever); ?>;
        content += start;
        if (job["spoed"] == 1) {
          var ja = " selected ";
          var nee = "";
        } else {
          var ja = "";
          var nee = " selected ";
        }
        content += "<div><label class='d-block mx-1 mb-0'>" + "PIN" + "</label><select class='form-input-small phone-width-100' required name='" + "pin" + "'><option " + nee + " value='0'>nee</option><option " + ja + " value='1'>ja</option></select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "betaalde bedrag" + "</label><input class='form-input-small phone-width-100' type='number' min='0' required step='0.01' value='" + job["bedrag"] + "' name='" + "bedrag" + "'/></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "factuur" + "</label><input class='form-input-big phone-width-100' type='number' name='" + "factuur" + "' value='" + job["factuur_nummer"] + "'/></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "uitgegever" + "</label><select class='form-input-big phone-width-100' required name='" + "uitgever" + "'>";
        for (var i = 0; i < uitgever.length; i++) {
          content += "<option" + selected + " value='" + uitgever[i].id + "'>" + uitgever[i].voornaam + " " + uitgever[i].achternaam + "</option>";
        }
        content += "</select></div>";
        content += end;
      }
      // 
      content += "<div class='d-flex mt-3'><input class='btn btn-outline-dark w-100 phone-mx-1' type='submit' value='+' /></div>";
      content += "<?php echo $actionBtn; ?>";
      content += "</a>"

      pageTitle = "wijzig";
      curPage = "btn-two";
      pageDisplay(content, pageTitle, curPage);
    } else if (pages[currentPage] == "PageThree") {
      if (job.status == 1) {
        const apparaten = <?php echo json_encode($apparaten); ?>;
        const uitbetalings_methode = <?php echo json_encode($uitbetalings_methodes); ?>;
        const bespanner = <?php echo json_encode($bespanner); ?>;
        content = "<form action='donejob.php?id=" + <?php echo $id ?> + "' method='post'><div class='content-container w-100 d-flex justify-content-center mt-3'><div class='phone-width-100 mx-1'>"
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "gebroken snaren" + "</label><select class='form-input-small phone-width-100' required name='" + "gebroken_snaren" + "'>";
        for (var i = 0; i < 6; i++) {
          content += "<option value='" + i + "'>" + i + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "racket breuk" + "</label><select class='form-input-small phone-width-100' required name='" + "racket_breuk" + "'><option value='0'>nee</option><option value='1'>ja</option></select></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "apparaat" + "</label><select class='form-input-small phone-width-100' required name='" + "apparaat" + "'>";
        for (var i = 0; i < apparaten.length; i++) {
          content += "<option value='" + apparaten[i].id + "'>" + apparaten[i].naam + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "uitbetaal methode" + "</label><select class='form-input-small phone-width-100' required name='" + "uitbetaal_methode" + "'>";
        for (var i = 0; i < uitbetalings_methode.length; i++) {
          content += "<option value='" + uitbetalings_methode[i].id + "'>" + "€" + uitbetalings_methode[i].geld + " " + uitbetalings_methode[i].type + "</option>";
        }
        content += "</select></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "bespanner" + "</label><select class='form-input-big phone-width-100' required name='" + "bespanner" + "'>";
        for (var i = 0; i < bespanner.length; i++) {
          content += "<option value='" + bespanner[i].id + "'>" + bespanner[i].voornaam + " " + bespanner[i].achternaam + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += "<div class='d-flex mt-3'><input class='btn btn-outline-dark w-100 phone-mx-1' type='submit' value='+' />";
        content += "</div></div>"
      } else if (job.status == 2) {
        const uitgever = <?php echo json_encode($uitgever); ?>;
        var start = "<div class='d-custom-flex phone-block mt-2'>";
        var end = "</div>";
        content = "<form action='donejob.php?id=" + <?php echo $id ?> + "' method='post'><div class='content-container w-100 d-flex justify-content-center mt-3'><div class='phone-width-100 mx-1'>"
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "PIN" + "</label><select class='form-input-small phone-width-100' required name='" + "pin" + "'><option value='0'>nee</option><option value='1'>ja</option></select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "betaalde bedrag" + "</label><input class='form-input-small phone-width-100' type='number' value='0' required min='0' step='0.01' name='" + "bedrag" + "'/></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "factuur" + "</label><input class='form-input-big phone-width-100' type='number' min='0' name='" + "factuur" + "'/></div>";
        content += end;
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "uitgegever" + "</label><select class='form-input-big phone-width-100' required name='" + "uitgever" + "'>";
        for (var i = 0; i < uitgever.length; i++) {
          content += "<option value='" + uitgever[i].id + "'>" + uitgever[i].voornaam + " " + uitgever[i].achternaam + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += "<div class='d-flex mt-3'><input class='btn btn-outline-dark w-100 phone-mx-1' type='submit' value='+' />";
        content += "</div></div>"
      } else if (job.status == 3) {
        var start = "<div class='d-custom-flex phone-block mt-2'>";
        var end = "</div>";
        content = "<form action='donejob.php?id=" + <?php echo $id ?> + "' method='post'><div class='content-container w-100 d-flex justify-content-center mt-3'><div class='phone-width-100 mx-1'>"
        // 
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "uitbetaald" + "</label><select class='form-input-big phone-width-100' required name='" + "uitbetaald" + "'><option value='0'>nee</option><option value='1'>ja</option></select></div>";
        content += end;
        // 
        content += "<div class='d-flex mt-3'><input class='btn btn-outline-dark w-100 phone-mx-1' type='submit' value='+' />";
        content += "</div></div>"
      }

      pageTitle = "klaar";
      curPage = "btn-three";
      pageDisplay(content, pageTitle, curPage);
    }
  }

  function checkResult(array, content) {
    if (array === "-") {
      return array;
    } else {
      return content;
    }
  }

  function contentLoop(names, stats) {
    rowStart = "<div class='custom-18 d-custom-flex phone-block'>";
    rowEnd = "</div>";
    counter = 0;
    lengthCheck(names.length);
    content = "<div class='custom-18 d-custom-flex phone-block mt-1'>";
    for (var i = 0; i < length; i++) {
      if (names[i]) {
        if (counter == 2) {
          content += rowEnd;
          content += rowStart;
          counter = 0;
        }
        if (stats[i]) {
          content += addContent(names[i], stats[i]);
        } else {
          content += addContent(names[i], "-");
        }
        counter++;
      } else if (counter = 1) {
        content += addContent("-", "-");
      }
    }
    content += rowEnd;
    return content;
  }

  function lengthCheck(arrayLength) {
    if (arrayLength % 2 == 0) {
      length = arrayLength;
    } else {
      length = arrayLength + 1;
    }
    return length;
  }

  function addContent(statName, stat) {
    return "<div class='custom-17 d-custom-flex phone-block p-3 m-1 my-0 w-100 justify-content-between text-center'><p class='mb-0 my-0 align-center'>" + statName + "</p><p class='mb-0 text-dark'>" + stat + "</p></div>";
  }

  // Displays the content of the page.
  function pageDisplay(content, pageTitle, curPage) {
    id("changeable-content").innerHTML = content;
    id("name-content-page").innerHTML = pageTitle;
    unselectSelector();
    selectedPage = curPage;
    selectSelector()
  }

  function resultCheck(item) {
    if (!item) {
      return 0;
    } else {
      return item;
    }
  }



</script>