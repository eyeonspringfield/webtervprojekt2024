<?php
session_start();
    include_once "php/admin_handler.php";
    include_once "php/newsletter_like_handler.php";

    if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"] && isset($_POST["cikktorles"])){
        remove_news($_POST["article_id"]);
    }
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Használt Autó Szeged - Hírlevél</title>
    <link rel="icon" href="img/logo.jpg">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/newsletter-content.css">
    <style>
        #newsletter{
            background-color: #4b0000;
        }
    </style>
    <script>
        function confirm_delete_news(){
            return confirm("Biztosan kitörli ezt a hírt? Vigyázzon, ez a művelet visszafordíthatatlan!");
        }
    </script>
</head>
<body>
<nav>
    <a href="index.php"><img src="img/logo.jpg" alt="Logo" height=40></a>
    <a class="navbutton" href="index.php">Főoldal</a>
    <a id="newsletter" class="navbutton" href="newsletter.php">Hírlevél</a>
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

<h1>Hírek</h1>

<div id="newscontainer">
    <?php
    if(isset($_SESSION["user"]) && isset($_POST["like"])){
        check_news_likes($_SESSION["user"], $_POST["likeid"]);
    }elseif(isset($_POST["like"])){
        echo '<p style="text-align: center">Jelentkezzen be a like-oláshoz!</p>';
    }
    if(isset($_SESSION["user"]) && isset($_POST["dislike"])){
        check_news_dislikes($_SESSION["user"], $_POST["dislikeid"]);
    }elseif(isset($_POST["dislike"])){
        echo '<p style="text-align: center">Jelentkezzen be a dislike-oláshoz!</p>';
    }

    $news = file_get_contents('data/news.json');
    $news_array = json_decode($news, true);
    foreach($news_array["news"] as $article){
        echo('<div class="contentnews">');
        echo('<h2> ' . $article["cim"] . '</h2>');
        echo('<h5> ' . $article["datum"] .' </h5>');
        echo('<p> ' . $article["szoveg"] . ' </p>');

        if(isset($_SESSION["user"]) && $_SESSION["user"]["admin"]) {
            echo('<form method="post" onsubmit="return confirm_delete_news()">');
            echo('<input type="hidden" name="article_id" value="' . $article["id"] . '">');
            echo('<button type="submit" name="cikktorles">Törlés</button>');
            echo('</form>');
        }

        echo('<div class="likedislike">');
        echo('<form method="post">');
        echo('<input type="hidden" value=" ' . $article["id"] . ' " name="likeid">');
        if(isset($_SESSION["user"])){
            if(check_if_newsliked($_SESSION["user"], $article["id"])){
                echo('<button type="submit" name="like" class="liked"><img src="img/likebutton.png" alt="like button" height="15"></button>');
            }else {
                echo('<button type="submit" name="like"><img src="img/likebutton.png" alt="like button" height="15"></button>');
            }
        }else{
            echo('<button type="submit" name="like"><img src="img/likebutton.png" alt="like button" height="15"></button>');
        }
        echo('<p id="likecount' . $article["id"] . '" class="likecount">' . $article["likes"] . '</p>');
        echo('</form>');
        echo('<form method="post">');
        echo('<input type="hidden" value=" ' . $article["id"] . ' " name="dislikeid">');
        if(isset($_SESSION["user"])){
            if(check_if_newsdisliked($_SESSION["user"], $article["id"])){
                echo('<button type="submit" name="dislike" class="liked"><img class="dislikeimg" src="img/likebutton.png" alt="dislike button" height="15"></button>');
            }else{
                echo('<button type="submit" name="dislike"><img class="dislikeimg" src="img/likebutton.png" alt="dislike button" height="15"></button>');
            }
        }else {
            echo('<button type="submit" name="dislike"><img class="dislikeimg" src="img/likebutton.png" alt="dislike button" height="15"></button>');
        }
        echo('<p id="dislikecount' . $article["id"] . '" class="dislikecount">' . $article["dislikes"] . '</p>');
        echo('</form>');
        echo('</div>');
        echo('</div>');

    }
    ?>

</div>

<footer>
    <p>Szegedi Használtautó Kft. &copy; 2024</p> <br>
    <p>Készítette: Csörgő Márk és Finta Róbert</p>
</footer>

</body>
</html>