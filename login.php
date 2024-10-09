<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: index.php?execute=no-permission");
}

include "php/user_manager.php";
$fiokok = load_users("data/users.json");

if(isset($_GET["execute"]) && $_GET["execute"] == "registered"){
    echo '<script>alert("Sikeres regisztráció")</script>';
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Bejelentkezés</title>
    <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        #login{
            background-color: #4b0000;
        }
    </style>
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
    <div class="form-container">
<form class="form" method="post">
    <h1 class="login-title">Bejelentkezés</h1>
    <?php
    if (isset($_POST["login"])) {
        if (!isset($_POST["username"]) || trim($_POST["username"]) === "") {
            echo "<strong>Hiba:</strong> Adj meg minden adatot!";
        } else if(!isset($_POST["password"]) || trim($_POST["password"]) === "") {
            echo "<strong>Hiba:</strong> Adj meg minden adatot!";
        }else{
            $felhasznalonev = $_POST["username"];
            $jelszo = $_POST["password"];


            foreach ($fiokok["users"] as $fiok) {
                if ($fiok["username"] === $felhasznalonev && password_verify($jelszo, $fiok["password"])) {
                    $uzenet = "Sikeres belépés!";
                    $_SESSION["user"] = $fiok;
                    header("Location: index.php");
                }else{
                    $siker = false;
                }
            }
            if(!$siker){
                echo "<p>Rossz felhasználónév vagy jelszó!<br></p>";
            }
        }
    }
    ?>
    <input type="text" class="login-input" name="username" placeholder="Felhasználónév" required/>
    <input type="password" class="login-input" name="password" placeholder="Jelszó" required/><br>
    <input type="reset" value="Törlés" name="submit" class="login-button"/>
    <input type="submit" value="Bejelenkezés" name="login" class="login-button"/>
    <p class="link">Nincs fiókod? <a href="register.php">Klikk Ide!</a></p>
</form>
    </div>

    <footer>
        <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
        <p>Készítette: Csörgő Márk és Finta Róbert</p>
    </footer>
</main>
</body>
</html>