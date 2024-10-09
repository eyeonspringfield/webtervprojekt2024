<?php
include_once "php/appointment_handler.php";
include_once "php/user_manager.php";
include_once "php/admin_handler.php";
session_start();
//echo session_id();
//var_dump($_SESSION);
if(!isset($_SESSION["user"])){
    header("Location: index.php?execute=not-logged-in");
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Profil</title>
    <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profile-content.css">
    <style>
        #profil{
            background-color: #4b0000;
        }
    </style>
    <script>
        function alert_not_logged_in(){
            alert("Kérjük, jelentkezzen be, hogy megtekinthesse profilját!");
        }
        function confirm_profile_delete(){
            confirm("Biztosan szeretné törölni profilját? Ez a folyamat visszafordíthatatlan, és az összes adata törlésre kerül, az időpontjait is beleértve!");
        }
    </script>
</head>
<body>
<nav>
    <a href="index.php"><img src="img/logo.jpg" alt="Logo" height=40></a>
    <a class="navbutton" href="index.php">Főoldal</a>
    <a class="navbutton" href="newsletter.php">Hírlevél</a>
    <a id="profil" class="navbutton" href="profile.php">Profil</a>
    <a class="navbutton" href="php/logout.php">Kijelentkezés</a>
    <?php
    if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]){
        echo '<a class="navbutton" href="admin.php">Admin</a>';
    }
    echo '<p class="navname">Bejelentkezve: ' . $_SESSION["user"]["vnev"] . " ". $_SESSION["user"]["knev"] . '</p>';
    ?>
</nav>

<main>

<h1 id="title">Profil</h1>

    <div id="messagecontainer">
        <h3>Üzenetek</h3>
        <?php
        if(isset($_POST["deletemessage"])){
            delete_user_message($_SESSION["user"]["username"], $_POST["message_text"]);
        }

        load_user_messages($_SESSION["user"]);
        ?>
    </div>

    <div class="profilcontainer">
    <div class="profbox">
        <h1>Adatok módosítása</h1>
        <div id="data">
            <img id="profpic" alt="profilepic" src="<?php echo $_SESSION["user"]["imgpath"]; ?>"><br>
            <h3>Felhasználónév: <?php echo $_SESSION["user"]["username"]; ?></h3>
            <h3>Név: <?php echo $_SESSION["user"]["vnev"] . ' ' . $_SESSION["user"]["knev"]; ?></h3>
            <h3>Email: <?php echo $_SESSION["user"]["email"]; ?></h3>
            <?php
            if($_SESSION["user"]["admin"]){
                echo '<h3 id="admintitle">Admin felhasználó</h3>';
            }
            ?>
        </div>
        <?php
        if(isset($_POST["changepswd"])){
            $errors = [];
            if(!isset($_POST["oldpswd"]) || trim($_POST["oldpswd"]) === ""){
                $errors[] = "Nem adott meg régi jelszót!";
            }
            if(!isset($_POST["newpswd"]) || trim($_POST["newpswd"]) === ""){
                $errors[] = "Nem adott meg új jelszót!";
            }
            if(!isset($_POST["newpswdagain"]) || trim($_POST["newpswdagain"]) === ""){
                $errors[] = "Nem adott meg jelszó ellenőrzőt!";
            }
            if($_POST["newpswd"] !== $_POST["newpswdagain"]){
                $errors[] = "Nem egyezik az új jelszó és a jelszó ellenőrző";
            }
            if(!password_verify($_POST["oldpswd"], $_SESSION["user"]["password"])){
                $errors[] = "Nem helyes a jelszó!";
            }
            if(count($errors) === 0) {
                echo "<p>Sikeres módisítás</p>";
                change_user_data($_SESSION["user"]["username"], "password", password_hash($_POST["newpswd"], PASSWORD_DEFAULT));
            }else{
                foreach($errors as $error){
                    echo "<p>$error</p><br>";
                }
            }
        }

        if(isset($_POST["changedata"])){
            $err = "";
            if((!isset($_POST["vnev"]) || trim($_POST["vnev"]) === "") && (!isset($_POST["knev"]) || trim($_POST["knev"]) === "") && (!isset($_POST["email"]) || trim($_POST["email"]) === "")){
                $err = "Nem adott meg semmilyen adatot!";
            }
            if($err === ""){
                if(isset($_POST["vnev"]) && trim($_POST["vnev"]) !== ""){
                    change_user_data($_SESSION["user"]["username"], "vnev", $_POST["vnev"]);
                    $_SESSION["user"]["vnev"] = $_POST["vnev"];
                }
                if(isset($_POST["knev"]) && trim($_POST["knev"]) !== ""){
                    change_user_data($_SESSION["user"]["username"], "knev", $_POST["knev"]);
                    $_SESSION["user"]["knev"] = $_POST["knev"];
                }
                if(isset($_POST["email"]) && trim($_POST["email"]) !== ""){
                    change_user_data($_SESSION["user"]["username"], "email", $_POST["email"]);
                    $_SESSION["user"]["email"] = $_POST["email"];
                }
                header("Location: profile.php");
            }
        }

        if(isset($_POST["changepfp"])){
            $errors = "";
            if(isset($_FILES["pfp"]) && $_FILES["pfp"]["error"] === UPLOAD_ERR_OK){
                if($_FILES["pfp"]["type"] !== "image/jpeg" && $_FILES["pfp"]["type"] !== "image/png"){
                    echo "Nem jó formátumba lett feltöltve a kép! (JPG, PNG)";
                    $errors = " 2";
                }
            }else{
                echo "Nem lett feltöltve kép!";
                $errors = " 2";
            }
            if($errors === ""){
                $img_file_extention = "";
                if($_FILES["pfp"]["type"] === "image/jpeg") {
                    $img_file_extention = '.jpg';
                } elseif($_FILES["pfp"]["type"] === "image/png"){
                    $img_file_extention = '.png';
                }
                $img_file_name = strtolower($_SESSION["user"]["username"]) . $img_file_extention;
                $directory = "img/pfp/";
                move_uploaded_file($_FILES["pfp"]["tmp_name"], $directory . $img_file_name);
                change_user_data($_SESSION["user"]["username"], "pfp", $directory . $img_file_name);
                $_SESSION["user"]["imgpath"] = $directory . $img_file_name;
                header("Location: profile.php");
            }
        }
        if(isset($_POST["deleteprofile"])){
            delete_profile($_SESSION["user"]);
        }
        ?>
    <form method="post">
        <input type="password" name="oldpswd" placeholder="Régi jelszó" required><br>
        <input type="password" name="newpswd" placeholder="Új jelszó" required><br>
        <input type="password" name="newpswdagain" placeholder="Új jelszó újra" required><br>
        <input class="submiter" type="reset" value="Törlés"><br>
        <input class="submiter" type="submit" name="changepswd" value="Jelszó módosítása"><br>
    </form>
        <br>
        <form method="post">
            <input type="text" name="vnev" placeholder="Vezetéknév"><br>
            <input type="text" name="knev" placeholder="Keresztnév"><br>
            <input type="email" name="email" placeholder="Email"><br>
            <input class="submiter" type="reset" value="Törlés"><br>
            <input class="submiter" type="submit" name="changedata" value="Adatok módosítása"><br>
        </form>
        <form method="post" enctype="multipart/form-data">
            <label>Új profilkép: <input type="file" name="pfp"></label>
            <input class="submiter" type="submit" name="changepfp" value="Profilkép feltöltése">
        </form>
        <form method="post" onsubmit="return confirm_profile_delete()">
            <input class="submiter" type="submit" name="deleteprofile" value="Profil törlése">
        </form>
    </div>

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
                    "usermade" => true,
                    "approved" => false,
                    "seller" => $_SESSION["user"]["vnev"] . ' ' . $_SESSION["user"]["knev"],
                    "sellerusername" => $_SESSION["user"]["username"]
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

                header("Location: profile.php");
            }else{
                foreach($errors as $error){
                    echo("<p class='error'> Hiba! " . $error . "</p><br>");
                }
            }
        }
        ?>

        <div class="profbox">
            <form class="form" method="post" name="ujauto" enctype="multipart/form-data" onsubmit="return confirm_car_upload()">
                <h1>Saját autó meghírdetése</h1>
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
                <input type="reset" value="Törlés" name="submit" class="submiter"/>
                <input type="submit" value="Feltöltés" name="submitujauto" class="submiter"/>
                <p>Kérjük, figyeljen, hogy minden mezőt megfelelően töltsön ki! A hiányos vagy hibás hirdetések elutasításra kerülnek!</p>
            </form>
        </div>

    <div class="profbox">
        <h1>Lefoglalt időpontok</h1>
        <div id="idopontok">
            <?php
                if(isset($_POST["deleteapp"])){
                    if(!empty($_POST["appointments"])){
                        deleteAppointment($_SESSION["user"]["username"], $_POST["appointments"]);
                    }else{
                        echo "<p>Nem választott időpontot!</p>";
                    }
                }
            ?>
            <form method="post">
                <?php
                load_appointments_on_profile($_SESSION);
                ?>
            </form>
        </div>
    </div>
    </div>



<footer>
    <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
    <p>Készítette: Csörgő Márk és Finta Róbert</p>
</footer>

</main>
</body>
</html>