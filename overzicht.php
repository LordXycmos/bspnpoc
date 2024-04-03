<?php
// BASICS
session_start();
include_once 'dbconnection.php';
include_once 'general-include.php';


// VARIABLES
$job = getJobs(0, $conn);
$jobSpoed = getJobs(1, $conn);
$jobDone = getDoneJobs($conn);
$customers = getCustomers($conn);
$bespankracht = getStrength($conn);
$snaartype = getStrings($conn);
$aannemer = getKassaEmployees($conn);
$opdrachtgever = getCustomerTypes($conn);
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
if ($role['rol'] == "kassa" || $role['rol'] == "allround" || $role['rol'] == "manager") {
  $addButton = "<p id='btn-three' class='custom-9 nav-unselected m-0 d-flex justify-content-center text-center py-1 text-dark'>+</p>";
  $afgeefButton = "<p id='btn-two' class='custom-9 nav-unselected m-0 d-flex justify-content-center text-center py-1 text-dark'>afgeven</p>";
  $addBtnActive = 1;
  $afgeefBtnActive = 1;
} else {
  $addButton = "<p id='btn-three'></p>";
  $afgeefButton = "<p id='btn-two'></p>";
  $addBtnActive = 0;
  $afgeefBtnActive = 0;
}

usort($job, function ($a, $b) {
  return strtotime($a['aanneem_datum']) - strtotime($b['aanneem_datum']);
});
usort($jobSpoed, function ($a, $b) {
  return strtotime($a['aanneem_datum']) - strtotime($b['aanneem_datum']);
});



// FUNCTIONS
function getJobs(int $spoed, $conn)
{
  $sql = "SELECT * FROM job WHERE spoed=? AND status=1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $spoed);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getDoneJobs($conn)
{
  $sql = "SELECT * FROM job WHERE status=2";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getCustomers($conn)
{
  $sql = "SELECT * FROM klant";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getStrength($conn)
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

function getCustomerTypes($conn)
{
  $sql = "SELECT * FROM klant_type";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getKassaEmployees($conn)
{
  $sql = "SELECT * FROM medewerker WHERE rol='kassa' OR rol='allround' OR rol='manager'";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
  <title>Overzicht - BeeS Bespanlijst</title>
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
      <div id="nav-three" class=" d-custom-none phone-block text-white">overzicht</div>
      <span class="custom-6 material-icons text-white">dns</span>
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
    <a href="manager-overzicht.php"
      class="custom-5 d-custom-none text-decoration-none justify-center text-center align-items-center p-3 phone-flex"
      id="nav-rapportages" <?php echo $managerButton; ?>>
      <div id="nav-six" class=" d-custom-none phone-block" style="color: #8e8e8e">overzicht</div>
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
        <p id="btn-one" class="custom-9 nav-selected m-0 d-flex justify-content-center text-center py-1 text-dark">
          bespannen
        </p>
        <?php echo $afgeefButton; ?>
      </div>
      <?php echo $addButton; ?>
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
  var pages = ["PageOne", "PageTwo", "PageThree"];
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

  for ($i = 0; $i < 3; $i++) {
    o("btn-one", 0);
    o("btn-two", 1);
    o("btn-three", 2);
  };

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

  function o(btn, curpage) {
    id(btn).addEventListener("click", function () {
      currentPage = curpage;
      selectPage();
    });
  }

  function selectPage() {
    if (pages[currentPage] == "PageOne") {
      var job = <?php echo json_encode($job); ?>;
      var jobSpoed = <?php echo json_encode($jobSpoed); ?>;
      content = "<p class='m-3 font-weight-bold text-dark'>spoed</p>"
      getContent(jobSpoed);
      content += "<p class='m-3 font-weight-bold text-dark'>geen spoed</p>"
      getContent(job);
      pageTitle = "bespannen";
      curPage = "btn-one";
      pageDisplay(content, pageTitle, curPage);
    } else if (pages[currentPage] == "PageTwo") {
      var isTrue = <?php echo json_encode($afgeefBtnActive); ?>;
      if (isTrue == 1) {
        var jobDone = <?php echo json_encode($jobDone); ?>;
        content = "<div class='m-3'></div>";
        getContent(jobDone);
        pageTitle = "afgeven";
        curPage = "btn-two";
        pageDisplay(content, pageTitle, curPage);
      } else {
        currentPage = 0;
      }
    } else if (pages[currentPage] == "PageThree") {
      var isTrue = <?php echo json_encode($addBtnActive); ?>;
      if (isTrue == 1) {
        var start = "<div class='d-custom-flex phone-block mt-2'>";
        var end = "</div>";
        const opdrachtgever = <?php echo json_encode($opdrachtgever); ?>;
        const bespankracht = <?php echo json_encode($bespankracht); ?>;
        const aannemer = <?php echo json_encode($aannemer); ?>;
        const snaartype = <?php echo json_encode($snaartype); ?>;
        const spoed = <?php echo json_encode($spoed); ?>;
        content = "<form action='creatingjob.php' method='post'><div class='content-container w-100 d-flex justify-content-center mt-3'><div class='phone-width-100 mx-1'>";
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "volgnummer" + "</label><input class='form-input-small phone-width-100' type='number' max='999' min='0' required name='" + "volgnummer" + "'/></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "opdrachtgever" + "</label><select class='form-input-small phone-width-100' required name='" + "opdrachtgever" + "'>";
        for (var i = 0; i < opdrachtgever.length; i++) {
          content += "<option value='" + opdrachtgever[i].id + "'>" + opdrachtgever[i].type + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "e-mail" + "</label><input maxlength='100' minlength='3' class='form-input-big phone-width-100' type='email' name='" + "email" + "' required/></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "type racket" + "</label><input maxlength='100' class='form-input-big phone-width-100' type='text' required name='" + "rackettype" + "'/></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "voornaam" + "</label><input maxlength='50' class='form-input-small phone-width-100' type='text' name='" + "voornaam" + "' required/></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "achternaam" + "</label><input maxlength='100' class='form-input-small phone-width-100' type='text' name='" + "achternaam" + "' required/></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "type snaar" + "</label><select class='form-input-big phone-width-100' required name='" + "snaartype" + "'>";
        for (var i = 0; i < snaartype.length; i++) {
          content += "<option value='" + snaartype[i].id + "'>" + snaartype[i].type + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "bespankracht lengte" + "</label><select class='form-input-small phone-width-100' required name='" + "kracht_lengte" + "'>";
        bespankracht.sort(function (a, b) {
          return a.bespankracht - b.bespankracht;
        });
        for (var i = 0; i < bespankracht.length; i++) {
          content += "<option value='" + bespankracht[i].id + "'>" + bespankracht[i].bespankracht + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "bespankracht breedte" + "</label><select class='form-input-small phone-width-100' required name='" + "kracht_breedte" + "'>";
        for (var i = 0; i < bespankracht.length; i++) {
          content += "<option value='" + bespankracht[i].id + "'>" + bespankracht[i].bespankracht + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "spoed" + "</label><select class='form-input-small phone-width-100' required name='" + "spoed" + "'>";
        for (var i = 0; i < spoed.length; i++) {
          content += "<option value='" + spoed[i].id + "'>" + spoed[i].bool + "</option>";
        }
        content += "</select></div>";
        content += "<div><label class='d-block mx-1 mb-0'>" + "prijs pinnen" + "</label><input class='form-input-small phone-width-100' value='0' type='number' min='0' step='0.01' name='" + "prijs" + "'/></div>";
        content += end;
        content += start;
        content += "<div><label class='d-block mx-1 mb-0'>" + "aannemer" + "</label><select class='form-input-big required phone-width-100' required name='" + "aannemer" + "'>";
        for (var i = 0; i < aannemer.length; i++) {
          content += "<option value='" + aannemer[i].id + "'>" + aannemer[i].voornaam + " " + aannemer[i].achternaam + "</option>";
        }
        content += "</select></div>";
        content += end;
        content += "<div class='d-flex mt-3'><input class='phone-mx-1 btn btn-outline-dark w-100' type='submit' value='+' />";
        content += "</div></div>";
        pageTitle = "+";
        curPage = "btn-three";
        pageDisplay(content, pageTitle, curPage);
      } else {
        currentPage = 1;
      }
    }
  }

  function pageDisplay(content, pageTitle, curPage) {
    id("changeable-content").innerHTML = content;
    id("name-content-page").innerHTML = pageTitle;
    unselectSelector();
    selectedPage = curPage;
    selectSelector()
  }

  function truncateString(str, maxLength) {
    if (str.length > maxLength) {
      return str.slice(0, maxLength - 3) + '...';
    }
    return str;
  }

  function getContent(array) {
    const bespankracht = <?php echo json_encode($bespankracht); ?>;
    const snaartype = <?php echo json_encode($snaartype); ?>;
    const klanten = <?php echo json_encode($customers); ?>;

    for (var i = 0; i < array.length; i++) {
      curBespankrachtLengte = array[i].kracht_lengte - 1;
      curBespankrachtBreedte = array[i].kracht_breedte - 1;
      curSnaar = array[i].snaar - 1;
      var email = klanten.find(item => item.id === array[i].klant);

      content += "<a href='overzicht-weergeven.php?id=" + array[i].id + "' class='text-decoration-none'><article class='custom-19 d-custom-flex phone-block p-3 my-1 mx-3 text-muted text-decoration-none'><p class='w-100 mb-0 phone-text-dark'>" + resultCheck(truncateString(snaartype[curSnaar].type, 20)) + " | L" + resultCheck(bespankracht[curBespankrachtLengte].bespankracht) + " | B" + resultCheck(bespankracht[curBespankrachtBreedte].bespankracht) + "</p><div class='d-flex w-100'><p class='w-100 mb-0'>" + resultCheck(truncateString(email.email, 20)) + "</p><p class='mb-0 phone-font-weight-bold'>" + resultCheck(array[i].volgnummer) + "</p></div></article></a>";
    }
  }

  function resultCheck(item) {
    if (!item) {
      return 0;
    } else {
      return item;
    }
  }


</script>