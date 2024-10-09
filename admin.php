<?php
session_start();

include_once "php/admin_handler.php";
include_once "php/appointment_handler.php";
include_once "php/show_cars.php";

if(!$_SESSION["user"]["admin"]){
    header("Location: index.php?execute=not-admin");
}
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Admin</title>
  <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin-content.css">
    <link rel="stylesheet" href="css/login-style.css">
    <style>
      #admin{
        background-color: #4b0000;
      }
    </style>
    <script>
        function confirm_car_upload(){
            return confirm("Biztosan feltölti az autót?");
        }
        function confirm_news_upload(){
            return confirm("Biztosan feltölti a hírt?");
        }
        function confirm_appointments_delete(){
            return confirm("Biztosan törli az ezen a napon lévő időpontokat?");
        }
    </script>
</head>
<body>

<nav>
    <a href="index.php"><img src="img/logo.jpg" alt="Logo" height=40></a>
    <a id="fooldal" class="navbutton" href="index.php">Főoldal</a>
    <a class="navbutton" href="newsletter.php">Hírlevél</a>
    <?php
    if(isset($_SESSION["user"])){
        echo '<a class="navbutton" href="profile.php">Profil</a>';
        echo '<a class="navbutton" href="php/logout.php">Kijelentkezés</a>';
    }else{
        echo '<a class="navbutton" href="login.php">Bejelentkezés</a>';
        echo '<a class="navbutton" href="register.php">Regisztráció</a>';
    }
    if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]){
        echo '<a class="navbutton" href="admin.php">Admin</a>';
    }
    ?>
</nav>

<main>

  <h1 id="cim">Admin panel</h1>

  <p id="adminleiras">Ezt a weboldalt csak az admin jogosultsággal rendelkező felhasználók érhetik el. Ha nem rendelkezik admin jogosultsággal, és mégis elérte ezt a weboldalt, kérjük, jelezze ezt a Használt Autó Szeged vezetősége felé!</p>
  <p id="email">jelentes@hasznaltautoszeged.hu</p>



  <div id="adminbox">
    <div class="form-container">
      <form class="form" method="post" name="ujauto" enctype="multipart/form-data" onsubmit="return confirm_car_upload()">
        <h1 class="login-title">Új autó közzététele</h1>

          <?php
          if(isset($_POST["submitujauto"])){

              $data = [];
              $errors = [];

              if(isset($_FILES["autokep"]) && $_FILES["autokep"]["error"] === UPLOAD_ERR_OK){
                  if($_FILES["autokep"]["type"] !== "image/jpeg" && $_FILES["autokep"]["type"] !== "image/png"){
                      $errors[] = "Nem jó formátumban lett feltöltve a kép!";
                  }
              }else{
                  $errors[] = "Nem lett feltöltve kép!";
              }

              if(!isset($_POST["marka"]) || trim($_POST["marka"]) === ""){
                  $errors[] = "Nem adott meg márkát!";
              }
              if(!isset($_POST["tipus"]) || trim($_POST["tipus"]) === ""){
                  $errors[] = "Nem adott meg típust!";
              }
              if(!isset($_POST["evjarat"]) || trim($_POST["evjarat"]) === ""){
                  $errors[] = "Nem adott meg évjáratot!";
              }
              if(!isset($_POST["rovleiras"]) || trim($_POST["rovleiras"]) === ""){
                  $errors[] = "Nem adott meg rövid leírást!";
              }
              if(!isset($_POST["ar"]) || trim($_POST["ar"]) === ""){
                  $errors[] = "Nem adott meg árat!";
              }
              if(!isset($_POST["autoleiras"]) || trim($_POST["autoleiras"]) === ""){
                  $errors[] = "Nem adott meg autó leírást!";
              }
              if(!isset($_POST["motor"]) || trim($_POST["motor"]) === ""){
                  $errors[] = "Nem adott meg motor jellemzőt!";
              }
              if(!isset($_POST["kilometer"]) || trim($_POST["kilometer"]) === ""){
                  $errors[] = "Nem adott meg kilométeróra állást!";
              }
              if(!isset($_POST["fogyasztas"]) || trim($_POST["fogyasztas"]) === ""){
                  $errors[] = "Nem adott meg fogyasztást!";
              }
              if(!isset($_POST["audio"]) || trim($_POST["audio"]) === ""){
                  $errors[] = "Nem adott meg audió jellemzőt!";
              }
              if(!isset($_POST["egyeb"]) || trim($_POST["egyeb"]) === ""){
                  $errors[] = "Nem adott meg egyéb jellemzőt!";
              }

              if($_POST["evjarat"] > intval(date('Y'))){
                  $errors[] = "Jövőbeli évjáratot adott meg!";
              }
              if(mb_strlen($_POST["autoleiras"], 'UTF-8') > 170){
                  $errors[] = "Túl hosszú autó leirást adott meg!";
              }

              if(sizeof($errors) === 0) {
                  $data = [
                      "marka" => $_POST["marka"],
                      "tipus" => $_POST["tipus"],
                      "evjarat" => $_POST["evjarat"],
                      "rovleiras" => $_POST["rovleiras"],
                      "ar" => $_POST["ar"],
                      "autoleiras" => $_POST["autoleiras"],
                      "motor" => $_POST["motor"],
                      "kilometer" => $_POST["kilometer"],
                      "fogyasztas" => $_POST["fogyasztas"],
                      "audio" => $_POST["audio"],
                      "egyeb" => $_POST["egyeb"],
                      "usermade" => false,
                      "approved" => true,
                      "seller" => "",
                      "sellerusername" => ""
                  ];

                  $img_file_extention = "";
                  if($_FILES["autokep"]["type"] === "image/jpeg") {
                      $img_file_extention = '.jpg';
                  } elseif($_FILES["autokep"]["type"] === "image/png"){
                      $img_file_extention = '.png';
                  }
                  $img_file_name = str_replace(' ','', strtolower($data["marka"])) . str_replace(' ','',strtolower($data["tipus"])) . $img_file_extention;
                  $directory = "img/";
                  move_uploaded_file($_FILES["autokep"]["tmp_name"], $directory . $img_file_name);

                  $data["ar"] = str_replace(',', ' ', number_format($data["ar"]));

                  add_car($data, $img_file_extention);

                  echo("<p> Sikeres feltöltés! </p>");
              }else{
                  foreach($errors as $error){
                      echo("<p class='error'> Hiba! " . $error . "</p><br>");
                  }
              }
          }
          ?>

        <input type="text" class="login-input" name="marka" placeholder="Márka" autofocus required/>
        <input type="text" class="login-input" name="tipus" placeholder="Típus" required/><br>
        <input type="number" class="login-input" name="evjarat" placeholder="Évjárat" required/><br>
        <input type="text" class="login-input" name="rovleiras" placeholder="Rövid leírás" required/><br>
        <input type="number" class="login-input" name="ar" placeholder="Ár" required/><br>
        <textarea class="login-input" name="autoleiras" placeholder="Leírás (max 170 kar.)" maxlength="170" required></textarea>
        <input type="text" class="login-input" name="motor" placeholder="Motor jellemzői" required/><br>
        <input type="text" class="login-input" name="kilometer" placeholder="Kilóméteróra állása" required/><br>
        <input type="text" class="login-input" name="fogyasztas" placeholder="Fogyasztás" required/><br>
        <input type="text" class="login-input" name="audio" placeholder="Audió felszereltség" required/><br>
        <input type="text" class="login-input" name="egyeb" placeholder="Egyéb jellemző" required/><br>
        <label>Kép: <input type="file" name="autokep" id="kep"></label>
        <input type="reset" value="Törlés" name="submit" class="login-button"/>
        <input type="submit" value="Feltöltés" name="submitujauto" class="login-button"/>
      </form>
    </div>


    <div class="form-container">
      <form class="form" method="post" name="ujhir" onsubmit="return confirm_news_upload()">
        <h1 class="login-title">Új hír közzététele</h1>

          <?php
          if(isset($_POST["submitujhir"])) {
              $errors = [];

              if (!isset($_POST["cim"]) || trim($_POST["cim"] === "")) {
                  $errors[] = "Nem adott meg címet!";
              }
              if (!isset($_POST["hirszoveg"]) || trim($_POST["hirszoveg"] === "")) {
                  $errors[] = "Nem adott meg szöveget!";
              }

              if (mb_strlen($_POST["hirszoveg"], 'UTF-8') > 750) {
                  $errors[] = "Túl hosszú szöveget adott meg!";
              }

              if (strlen($_POST["datum"]) !== 0 && !preg_match("/^\d{4}\.\d{2}\.\d{2}$/", $_POST["datum"])) {
                  $errors[] = "Nincs megfelelő formátumban a dátum!";
              }

              if (strlen($_POST["datum"]) !== 0 && strtotime($_POST["datum"]) > time()) {
                  $errors[] = "Jövőbeli dátumot adott meg!";
              }

              $cim = $_POST["cim"];
              $szoveg = $_POST["hirszoveg"];
              if (strlen($_POST["datum"]) === 0) {
                  $datum = date('Y.m.d');
              } else {
                  $datum = $_POST["datum"];
              }

              if (sizeof($errors) === 0) {
                  add_news($cim, $szoveg, $datum);
              }else{
                  foreach($errors as $error){
                      echo("<br><p class='error'> Hiba! " . $error . "</p>");
                  }
              }
          }
          ?>

        <input type="text" class="login-input" name="cim" placeholder="Cím" required/>
        <textarea class="login-input hirszoveg" name="hirszoveg" placeholder="Szöveg (max 750 kar.)" maxlength="750" required></textarea>
        <input type="text" class="login-input" name="datum" placeholder="Dátum (NN/HH/ÉÉÉÉ, üresen mai dátum)" /><br>
        <input type="reset" value="Törlés" name="reset" class="login-button"/>
        <input type="submit" value="Feltöltés" name="submitujhir" class="login-button"/>
      </form>
    </div>

    <div class="form-container" id="datumtorles">
      <form class="form" method="post" name="login">
        <h1 class="login-title">Időpont(ok) törlése</h1>
          <?php
          if(isset($_POST["submitdeleteappointments"])){
              if(!isset($_POST["idopontdatum"])){
                  echo("Nem adott meg dátumot!");
              }else{
                  admin_delete_appointments($_POST["idopontdatum"]);
              }
          }
          ?>
        <input type="date" class="login-input" name="idopontdatum" required/>
        <input type="reset" value="Mégse" name="submit" class="login-button"/>
        <input type="submit" value="Foglalt időpont(ok) törlése" name="submitdeleteappointments" class="login-button"/>
      </form>
    </div>
  </div>

    <h1>Jóváhagyásra váró autók:</h1>

    <?php
    if(isset($_POST["approvecar"])){
        modify_car_approval($_POST["car_id"], true, "data/cars.json");
    }
    ?>

    <div id="kinalat">
        <?php show_cars_on_admin_page(); ?>
    </div>





  <footer>
    <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
    <p>Készítette: Csörgő Márk és Finta Róbert</p>
  </footer>
</main>

</body>
</html>