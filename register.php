<?php
include "php/user_manager.php";
session_start();

if(isset($_SESSION["user"])){
    header("Location: index.php?execute=logged-in");
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Regisztráció</title>
    <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/login-style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        #regiszt{
            background-color: #4b0000;
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php"><img src="img/logo.jpg" alt="Logo" height=40></a>
    <a id="fooldal" class="navbutton" href="index.php">Főoldal</a>
    <a class="navbutton" href="newsletter.php">Hírlevél</a>
    <a class="navbutton" href="login.php">Bejelentkezés</a>
    <a id="regiszt" class="navbutton" href="register.php">Regisztráció</a>
</nav>

<main>
<div class="form-container">
<form class="form" method="post">
    <h1 class="login-title">Regisztráció</h1>
    <?php
    $hibakTomb = [];

    $felhasznalok = load_users("data/users.json");

    if(isset($_POST["regiszt"])){
        if (!isset($_POST["username"]) || trim($_POST["username"]) === "")
            $hibakTomb[] = "Felhasználónév hiányzik!";

        if (!isset($_POST["email"]) || trim($_POST["email"]) === "")
            $hibakTomb[] = "E-mail hiányzik!";

        if (!isset($_POST["password"]) || trim($_POST["password"]) === "" || !isset($_POST["passwordcnf"]) || trim($_POST["passwordcnf"]) === "")
            $hibakTomb[] = "Jelszó hiányzik";

        if (!isset($_POST["vezetekn"]) || trim($_POST["vezetekn"]) === "")
            $hibakTomb[] = "Vezetéknév hiányzik!";

        if (!isset($_POST["keresztn"]) || trim($_POST["keresztn"]) === "")
            $hibakTomb[] = "Keresztnév hiányzik!";

        $felhasznev = $_POST["username"];
        $jelszo = $_POST["password"];
        $jelszoCnf = $_POST["passwordcnf"];
        $vNev = $_POST["vezetekn"];
        $kNev = $_POST["keresztn"];
        $email = $_POST["email"];

        if ($jelszo !== $jelszoCnf){
            $hibakTomb[] = "A jelszó és az ellenőrző jelszó nem egyezik!";
        }

        foreach ($felhasznalok["users"] as $fiok) {
            if ($fiok["username"] == $_POST["username"]){
                $hibakTomb[] = "A felhasználónév már foglalt!";
            }
        }

        if (count($hibakTomb) === 0){
            $felhasznalo = [
                "username" => $felhasznev,
                "email" => $email,
                "password" => password_hash($jelszo, PASSWORD_DEFAULT),
                "vnev" => $vNev,
                "knev" => $kNev,
                "admin" => false,
                "imgpath" => "img/pfp/default-avatar.png",
                "carlikes" => "",
                "cardislikes" => "",
                "newslikes" => "",
                "newsdislikes" => "",
                "appointments" => "",
                "messages" => ""
            ];
            save_users("data/users.json", $felhasznalo);
            header("Location: login.php?execute=registered");
        }else{
           // foreach ($hibakTomb as $hiba){
               // echo $hiba;
           // }
        }
    }
    ?>
    <input type="text" class="login-input" name="username" placeholder="Felhasználónév" required />
    <input type="email" class="login-input" name="email" placeholder="Email">
    <input type="text" class="login-input" name="vezetekn" placeholder="Vezetéknév">
    <input type="text" class="login-input" name="keresztn" placeholder="Keresztnév">
    <input type="password" class="login-input" name="password" placeholder="Jelszó">
    <input type="password" class="login-input" name="passwordcnf" placeholder="Jelszó újra"><br>
    <input type="reset" value="Törlés" name="submit" class="login-button"/>
    <input type="submit" name="regiszt" value="Regisztrálás" class="login-button">
    <p class="link">Már regisztráltál? <a href="login.php">Klikk ide!</a></p>
</form>
</div>

    <footer>
        <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
        <p>Készítette: Csörgő Márk és Finta Róbert</p>
    </footer>

</main>

</body>
</html>