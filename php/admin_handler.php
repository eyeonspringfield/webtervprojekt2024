<?php
include_once "user_manager.php";
function add_news($cim, $szoveg, $datum){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $article_num = $news_array["news"][0]["id"];
    $uj_cikk = [
        "cim" => $cim,
        "datum" => $datum,
        "szoveg" => $szoveg,
        "likes" => 0,
        "dislikes" => 0,
        "id" => ++$article_num
    ];
    array_unshift($news_array["news"], $uj_cikk);
    $uj_news_json = json_encode($news_array, JSON_PRETTY_PRINT);
    file_put_contents("data/news.json", $uj_news_json);
}

function remove_news($id){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $index = array_search($id, array_column($news_array["news"], "id"));
    if($index !== false) {
        delete_likes_from_users($id, "news");
        array_splice($news_array["news"], $index, 1);
        $news_array["news"] = array_values($news_array["news"]);
        $new_news_array = json_encode($news_array, JSON_PRETTY_PRINT);
        file_put_contents("data/news.json", $new_news_array);
    }else{
        echo("<h1>Hiba a hír eltávolításakor!</h1>");
    }
}

function add_car($data, $img_file_extention){
    $cars_json = file_get_contents("data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $car_num = end($cars_array["cars"])["id"];
    $new_car = [
        "marka" => $data["marka"],
        "tipus" => $data["tipus"],
        "evjarat" => $data["evjarat"],
        "rovleiras" => $data["rovleiras"],
        "ar" => $data["ar"],
        "autoleiras" => $data["autoleiras"],
        "motor" => $data["motor"],
        "kilometer" => $data["kilometer"],
        "fogyasztas" => $data["fogyasztas"],
        "audio" => $data["audio"],
        "egyeb" => $data["egyeb"],
        "imgpath" => '../img/' . str_replace(' ','', strtolower($data["marka"])) . str_replace(' ','',strtolower($data["tipus"])) . $img_file_extention,
        "likes" => 0,
        "dislikes" => 0,
        "id" => ++$car_num,
        "usermade" => $data["usermade"],
        "approved" => $data["approved"],
        "seller" => $data["seller"],
        "sellerusername" => $data["sellerusername"]
        ];
    if($data["usermade"]){
        send_user_message($data["sellerusername"],"data/users.json", "Feltöltésre került a(z) " . $data["marka"] . " " . $data["tipus"] . " " . $data["evjarat"] .  " autója! Értesíteni fogjúk jóváhagyási státuszáról!");
    }
    $cars_array["cars"][] = $new_car;
    $new_cars_json = json_encode($cars_array, JSON_PRETTY_PRINT);
    file_put_contents("data/cars.json", $new_cars_json);
    create_car_page($data, $car_num, $img_file_extention);
}
function create_car_page($data, $id, $img_file_extention){
    if($data === null){
        die("Hiba történt!");
    }
    $file_name = 'auto-' . str_replace(' ','', strtolower($data["marka"])) . '-' . str_replace(' ','',strtolower($data["tipus"])) . '.php';
    $image = '../img/' . str_replace(' ','', strtolower($data["marka"])) . str_replace(' ','',strtolower($data["tipus"])) . $img_file_extention;
    $new_file_directory = "auto/";
    $new_file_name = $new_file_directory . $file_name;
    var_dump($data["usermade"]);
    $car_data = '<?php
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
        echo "<a class=\"navbutton\" href=\"../profile.php\">Profil</a>";
        echo "<a class=\"navbutton\" href=\"../php/logout.php\">Kijelentkezés</a>";
    }else{
        echo "<a class=\"navbutton\" href=\"../login.php\">Bejelentkezés</a>";
        echo "<a class=\"navbutton\" href=\"../register.php\">Regisztráció</a>";
    }
    if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]){
        echo "<a class=\"navbutton\" href=\"../admin.php\">Admin</a>";
    }
    ?>
</nav>

<main>
  <br>
  <h3 id="auto-nev">' . $data["marka"] . ' ' . $data["tipus"] . ' ' . $data["evjarat"] . '</h3>
  
  <?php
    if(isset($_SESSION["user"]) && isset($_POST["like"])){
        check_likes($_SESSION["user"], ' . $id . ');
    }else if(isset($_POST["like"])){
        echo "<p style=\"text-align: center; color: red\">Jelentkezzen be a likeoláshoz!</p>";
    }

    if(isset($_SESSION["user"]) && isset($_POST["dislike"])){
        check_dislikes($_SESSION["user"], ' . $id . ');
    }else if(isset($_POST["dislike"])){
        echo "<p style=\"text-align: center; color: red\">Jelentkezzen be a dislikeoláshoz!</p>";
    }
    ?>

  <div class="autobox">
    <img src="' . $image . '" alt="auto kép" height="250">
    <p>' . $data["autoleiras"] . '</p>
    <br>
    <ul>
      <li>' . $data["motor"] . '</li>
      <li>' . $data["kilometer"] . '</li>
      <li>' . $data["fogyasztas"] . '</li>
      <li>' . $data["audio"] . '</li>
      <li>' . $data["egyeb"] . '</li>
    </ul>


    <p id="price">' . $data["ar"] . ' HUF</p>

    <?php
      if(isset($_SESSION["user"])){
          load_likes_logged_in($_SESSION["user"], ' . $id . ');
      }else {
          load_likes(' . $id . ');
      }
      ?>

    <br>
    <?php
    if(isset($_POST["appointment"])){
          addAppointment($_SESSION["user"]["username"], "' . $data["marka"] . " " . $data["tipus"] . " " . $data["evjarat"] . '", $_POST["appointmentdate"]);
      }
      if(isset($_SESSION["user"])) {
          echo"<p id=\"idotext\"> Kérjük, foglaljon időpontot az autó megtekintéséhez!</p>";
          echo "<form method=\"post\">";
          echo "<label for=\"date\" > Dátum: </label ><input id = \"date\" name=\"appointmentdate\" type = \"date\" required >";
          echo"<input type = \"button\" id = \"idopont\" name=\"appointment\" value = \"Időpontfoglalás\" >";
          echo"</form >";
      }else{
          echo"<p id=\"idotext\" style=\"padding-bottom: 40px\">Kérjük, jelentkezzen be időpont foglalásához!</p>";
      }
      
      if("' . $data["usermade"] . '" === "1"){
        echo"<p>Eladó: ' . $data["seller"] . '</p>";
      }
      
      if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]) {
          echo"<form method=\"post\" onsubmit=\"return confirm_delete_car()\">";
          echo"<input type=\"hidden\" name=\"article_id\" value=\"' . $id . '\">";
          echo"<button type=\"submit\" name=\"cikktorles\">Törlés</button>";
          echo"</form>";
          if("' . $data["usermade"] . '" === "1"){
          echo"<br>";
          echo"<form method=\"post\" onsubmit=\"return confirm_approval_remove()\">";
          echo"<input type=\"hidden\" name=\"car_id\" value=\"5\">";
          echo"<button type=\"submit\" name=\"removeapproval\">Jóváhagyás visszavonása</button>";
          echo"</form>";
      }
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
</html>';

    file_put_contents($new_file_name, $car_data);
}

function remove_cars($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    foreach($cars_array["cars"] as $car){
        if($car["id"] == $id && $car["usermade"] && !$car["approved"]){
            send_user_message($car["sellerusername"],"../data/users.json", "" . $car["marka"] . " " . $car["tipus"] . " " . $car["evjarat"] . " autója elutasításra került! Kérjük, javítsa a hibákat a hirdetésén, és próbálja újra!");
        }
        if($car["id"] == $id && $car["usermade"] && $car["approved"]){
            send_user_message($car["sellerusername"],"../data/users.json", "" . $car["marka"] . " " . $car["tipus"] . " " . $car["evjarat"] . " autója törlésre került!");
        }
    }
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        delete_likes_from_users($id, "cars");
        delete_car_files($id, "../data/cars.json", true);
        array_splice($cars_array["cars"], $index, 1);
        $cars_array["cars"] = array_values($cars_array["cars"]);
        $new_cars_array = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("../data/cars.json", $new_cars_array);
    }else{
        echo("<h1>Hiba az autó törlésekor!</h1>");
    }
}

function remove_cars_of_user($id){
    $cars_json = file_get_contents("data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        delete_car_files($id, "data/cars.json", false);
        array_splice($cars_array["cars"], $index, 1);
        $cars_array["cars"] = array_values($cars_array["cars"]);
        $new_cars_array = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("data/cars.json", $new_cars_array);
    }else{
        echo("<h1>Hiba az autó törlésekor!</h1>");
    }
}

function delete_car_files($id, $path, $from_car_page){
    $cars_json = file_get_contents($path);
    $cars_array = json_decode($cars_json, true);

    $index = array_search($id, array_column($cars_array["cars"], "id"));

    if($index !== false) {
        if($from_car_page){
            $url = 'auto-' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["marka"])) . '-' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["tipus"])) . '.php';
            $img = '../img/' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["marka"])) . str_replace(' ', '', strtolower($cars_array["cars"][$index]["tipus"]));
        }else {
            $url = 'auto/auto-' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["marka"])) . '-' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["tipus"])) . '.php';
            $img = 'img/' . str_replace(' ', '', strtolower($cars_array["cars"][$index]["marka"])) . str_replace(' ', '', strtolower($cars_array["cars"][$index]["tipus"]));
        }

        if (file_exists($url)) {
            unlink($url);
        } else {
            echo("Nem található az oldal!");
        }
        if (file_exists($img . ".jpg")) {
            unlink($img . ".jpg");
        } elseif (file_exists($img . ".png")) {
            unlink($img . ".png");
        } else {
            echo("Nem található a kép!");
        }
    }else{
        echo("Nem található az autó!");
    }

}

function modify_car_approval($id, $type, $path){
    $cars_array = json_decode(file_get_contents($path), true);
    foreach($cars_array["cars"] as &$car){
        if($car["id"] == $id){
            $car["approved"] = $type;
            if($type) {
                send_user_message($car["sellerusername"], "data/users.json", "Jóváhagyták a(z) " . $car["marka"] . " " . $car["tipus"] . " " . $car["evjarat"] . " autóját!");
            }else{
                send_user_message($car["sellerusername"], "../data/users.json", "Visszavonták a jóváhagyást a(z) " . $car["marka"] . " " . $car["tipus"] . " " . $car["evjarat"] . " autójára!");
            }
            break;
        }
    }
    file_put_contents($path, json_encode($cars_array, JSON_PRETTY_PRINT));
}

function send_user_message($username, $path, $message){
    $user_array = json_decode(file_get_contents($path),true);
    foreach($user_array["users"] as &$user){
        if($username === $user["username"]){
            $messages_array = explode(":", $user["messages"]);
            $messages_array[] = $message;
            $new_messages = implode(":", $messages_array);
            $user["messages"] = $new_messages;
            break;
        }
    }
    file_put_contents($path, json_encode($user_array, JSON_PRETTY_PRINT));
}

function delete_user_message($username, $message){
    $user_array = json_decode(file_get_contents("data/users.json"),true);
    foreach($user_array["users"] as &$user){
        if($username === $user["username"]){
            $messages_array = explode(":", $user["messages"]);
            foreach($messages_array as $key => &$messages){
                if(str_replace(" ", "", $messages) == $message){
                    unset($messages_array[$key]);
                }
            }
            $new_messages_array = implode(":", $messages_array);
            $user["messages"] = $new_messages_array;
            break;
        }
    }
    file_put_contents("data/users.json", json_encode($user_array, JSON_PRETTY_PRINT));
}

function load_user_messages($session){
    $user_array = json_decode(file_get_contents("data/users.json"),true);
    $user_to_load = 0;
    foreach($user_array["users"] as $user){
        if($session["username"] === $user["username"]){
            $user_to_load = $user;
            break;
        }
    }
    $messages = explode(":", $user_to_load["messages"]);
    if(count($messages) > 1){
        foreach($messages as $message){
            if(!empty($message)) {
                echo "<div class=\"messagecontent\">";
                echo "<h4>" . $message . "</h4>";
                echo "<form method=\"post\">";
                echo "<input type=\"hidden\" value=" . str_replace(" ", "", $message) . " name=\"message_text\">";
                echo "<button type=\"submit\" class=\"delmessage\" name=\"deletemessage\">Törlés</button>";
                echo "</form>";
                echo "</div>";
            }
        }
    }else{
        echo"<h4>Önnek nincs jelenleg üzenete!</h4>";
    }
}