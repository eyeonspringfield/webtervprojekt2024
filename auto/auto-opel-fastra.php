<?php
session_start();
    include_once "../php/show_cars.php";
    include_once "../php/car_like_handler.php";
    include_once "../php/admin_handler.php";
    include_once "../php/appointment_handler.php";

if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"] && isset($_POST["cikktorles"])){
    remove_cars($_POST["article_id"]);
    header("Location: ../index.php");
}
if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"] && isset($_POST["removeapproval"])){
    modify_car_approval($_POST["car_id"], false, "../data/cars.json");
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Használt Autó Szeged - Autó</title>
  <link rel="icon" href="../img/logo.jpg">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/auto-content.css">
  <style>
    #on{
      background-color: lightgrey;
      color: black;
      transform: none;
    }

    #on > .price{
      background-color: lightgrey;
      box-shadow: none;
      color: black;
    }
  </style>
    <script>
        function confirm_delete_car(){
            return confirm("Biztosan kitörli ezt az autót? Vigyázzon, ez a művelet visszafordíthatatlan!");
        }
        function confirm_approval_remove(){
            return confirm("Biztosan visszavonja a jóváhagyását?");
        }
    </script>
</head>
<body>

<nav>
    <a href="../index.php"><img src="../img/logo.jpg" alt="Logo" height=40></a>
    <a id="fooldal" class="navbutton" href="../index.php">Főoldal</a>
    <a class="navbutton" href="../newsletter.php">Hírlevél</a>
    <?php
    if(isset($_SESSION["user"])){
        echo '<a class="navbutton" href="../profile.php">Profil</a>';
        echo '<a class="navbutton" href="../php/logout.php">Kijelentkezés</a>';
        if($_SESSION["user"]["admin"]){
            echo '<a class="navbutton" href="../admin.php">Admin</a>';
        }
        echo '<p class="navname">Bejelentkezve: ' . $_SESSION["user"]["vnev"] . " ". $_SESSION["user"]["knev"] . '</p>';
    }else{
        echo '<a class="navbutton" href="../login.php">Bejelentkezés</a>';
        echo '<a class="navbutton" href="../register.php">Regisztráció</a>';
    }
    ?>
</nav>

<main>
  <br>
  <h3 id="auto-nev">Opel F Astra 1996</h3>

    <?php
    if(isset($_SESSION["user"]) && isset($_POST["like"])){
        check_likes($_SESSION["user"], 1);
    }else if(isset($_POST["like"])){
        echo "<p style='text-align: center; color: red'>Jelentkezzen be a likeoláshoz!</p>";
    }

    if(isset($_SESSION["user"]) && isset($_POST["dislike"])){
        check_dislikes($_SESSION["user"], 1);
    }else if(isset($_POST["dislike"])){
        echo "<p style='text-align: center; color: red'>Jelentkezzen be a dislikeoláshoz!</p>";
    }
    ?>

  <div class="autobox">
    <img src="../img/opelfastra.jpg" alt="auto kép" height="250">
    <p>Eladóvá vált ez a megkímélt állapotú, 1996-os gyártmányú Opel F Astra. Elöző tulajdonosai sokat törödtek az autóval. Rendelkezik forgalmi engedéllyel.</p>
    <br>
    <ul>
      <li>1.4-es benzin motor</li>
      <li>170000 km</li>
      <li>Alacsony fogyasztás</li>
      <li>Ütős audio a 10 colos mélynyomónak köszönhetően</li>
      <li>2 év múlva akár old timer autó is lehetne!</li>
    </ul>


    <p id="price">749 990 HUF</p>

      <?php
      if(isset($_SESSION["user"])){
          load_likes_logged_in($_SESSION["user"], 1);
      }else {
          load_likes(1);
      }
      ?>

    <br>
      <?php
      if(isset($_POST["appointment"])){
          addAppointment($_SESSION["user"]["username"], "Opel F Astra 1996", $_POST["appointmentdate"]);
      }
      if(isset($_SESSION["user"])) {
          echo'<p id="idotext"> Kérjük, foglaljon időpontot az autó megtekintéséhez!</p>';
          echo '<form method="post">';
          echo '<label for="date"> Dátum: </label ><input id="date" type="date" name="appointmentdate" required >';
          echo'<input type="submit" id="idopont" name="appointment" value="Időpontfoglalás" >';
          echo'</form >';
    }else{
          echo'<p id="idotext" style="padding-bottom: 40px">Kérjük, jelentkezzen be időpont foglalásához!</p>';
      }
      ?>
      <p>Eladó: Szőnyi Miklós Gusztáv</p>
      <?php
      if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]) {
          echo'<form method="post" onsubmit="return confirm_delete_car()">';
          echo'<input type="hidden" name="article_id" value="1">';
          echo'<button type="submit" name="cikktorles">Törlés</button>';
          echo'</form>';
          echo "<br>";
          echo"<form method=\"post\" onsubmit=\"return confirm_approval_remove()\">";
          echo"<input type=\"hidden\" name=\"car_id\" value=\"1\">";
          echo"<button class=\"adminbutton\" type=\"submit\" name=\"removeapproval\">Jóváhagyás visszavonása</button>";
          echo"</form>";
      }
    ?>

  </div>

  <h1>Egyéb a kínálatunkból:</h1>

    <div id="kinalat">
        <?php
        show_cars_on_car_page();
        ?>
    </div>


  <footer>
    <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
    <p>Készítette: Csörgő Márk és Finta Róbert</p>
  </footer>
</main>
</body>
</html>