<?php
include_once "php/show_cars.php";

session_start();

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Főoldal</title>
    <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/main-content.css">
    <style>
        #fooldal{
            background-color: #4b0000;
        }
    </style>
    <?php
    if(isset($_GET["execute"]) && $_GET["execute"] == "not-logged-in"){
        echo '<script>alert("Jelentkezzen be, hogy megtekinthesse a profilját!")</script>';
    }
    if(isset($_GET["execute"]) && $_GET["execute"] == "not-admin"){
        echo '<script>alert("Jelentkezzen be egy admin jogosultsággal rendelkező fiókba, hogy megtekinthesse ezt az oldalt!")</script>';
    }
    if(isset($_GET["execute"]) && $_GET["execute"] == "car-deleted"){
        echo '<script>alert("Autó törölve!")</script>';
    }
    if(isset($_GET["execute"]) && $_GET["execute"] == "profile-deleted"){
        session_unset();
        session_destroy();
        echo '<script>alert("Profil törölve!")</script>';
    }
    if(isset($_GET["execute"]) && $_GET["execute"] == "logged-in"){
        echo '<script>alert("Jelentkezzen ki, hogy regisztrálhasson!")</script>';
    }
    if(isset($_GET["execute"]) && $_GET["execute"] == "no-permission"){
        echo '<script>alert("Jelentkezzen ki, mielött újra bejelentkezik!")</script>';
    }
    ?>
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
        if($_SESSION["user"]["admin"]){
            echo '<a class="navbutton" href="admin.php">Admin</a>';
        }
        echo '<p class="navname">Bejelentkezve: ' . $_SESSION["user"]["vnev"] . " ". $_SESSION["user"]["knev"] . '</p>';
    }else{
        echo '<a class="navbutton" href="login.php">Bejelentkezés</a>';
        echo '<a class="navbutton" href="register.php">Regisztráció</a>';
    }
    ?>

</nav>

<main>
    <div>
        <h1 id="title">Használt Autó Szeged</h1><br>
    </div>

    <div id="description">
        <p>Üdvözöljük a Használt Autó Szeged honlapján! Autókereskedésünk 2019 óta csakis a legjobb áron adja a legjobb minőségű használt autókat Szegeden! Munkatársainknak mindig az az első szempont, hogy elégedett legyen a vásárló, ezért csakis a legjobb szolgáltatásokra kell számítania, ha nálunk akar autót vásárolni. Részletes tájékoztatás és próbavezetéssel egyszerűen meggyőzödhet az autóink minőségéről. Kérjük vegye figyelembe, autóinkat csak időpontfoglalással lehet megtekinteni.</p><br>
    </div>

    <h2 class="header">Aktuális kínálatunk:</h2><br>

    <div id="kinalat">
        <?php
        show_cars_on_index();
        ?>
        </div>

    <h2 id="hol" class="header">Hol vagyunk megtalálhatóak?</h2>
    <p id="terkep">A lenti térképen láthatja, hol vagyunk megtalálhatóak. Címünk: 6725 Szeged, Tisza Lajos körút 103.</p> <br>
    <iframe id="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22074.47937104466!2d20.12422098010045!3d46.24406630825332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4744886557ac1407%3A0x8ef6cdceb30443a2!2sUniversity%20of%20Szeged%20Irinyi%20building!5e0!3m2!1sen!2shu!4v1710773180489!5m2!1sen!2shu" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

    <p id="nyitvatartas">Nyítvatartás: H-P: 8:00 - 16:00, Sz: 7:00 - 18:00, V: Zárva</p>

    <footer>
        <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
        <p>Készítette: Csörgő Márk és Finta Róbert</p>
    </footer>
</main>

</body>
</html>