<?php
include_once "car_like_handler.php";
include_once "newsletter_like_handler.php";
include_once "admin_handler.php";
function save_users($path, $data) {
    $users = load_users($path);

    $users["users"][] = $data;

    $json_data = json_encode($users, JSON_PRETTY_PRINT);

    file_put_contents($path, $json_data);
}
function load_users(string $path): array{

    if (!file_exists($path))
        die("No file or not opened!");

    $users = file_get_contents($path);
    return json_decode($users, true);
}

function change_user_data($username, $type, $data){
    $users = load_users("data/users.json");
    foreach($users["users"] as &$user){
        if($user["username"] === $username){
            switch($type){
                case "password":
                    $user["password"] = $data;
                    break;
                case "vnev":
                    $user["vnev"] = $data;
                    break;
                case "knev":
                    $user["knev"] = $data;
                    break;
                case "email":
                    $user["email"] = $data;
                    break;
                case "pfp":
                    $user["imgpath"] = $data;
                    break;
                default:
                    echo "what";
            }
            break;
        }
    }
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function delete_profile($session){
    $users = load_users("data/users.json");
    foreach($users["users"] as $index => $user){
        if($user["username"] == $session["username"]){
            echo "bruh";
            $refreshed_session = refresh_session($session); //Ha barmi valtozas tortenik ugyanabban a sessionben, mint amikor torlodne, akkor frissitessel elkerulheto a felreszamolas
            delete_user_cars($refreshed_session);
            delete_user_likes($refreshed_session);
            delete_user_pfp($refreshed_session);
            array_splice($users["users"], $index, 1);
            break;
        }
    }
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
    header("Location: index.php?execute=profile-deleted");
}

function refresh_session($session): array{
    $users = load_users("data/users.json");
    session_unset();
    session_destroy();
    foreach($users["users"] as $user){
        if($user["username"] == $session["username"]){
            $_SESSION["user"] = $user;
        }
    }
    return $_SESSION["user"];
}

function delete_likes_from_users($id, $type){
    if ($type == "news") {
        $users = load_users("data/users.json");
        foreach ($users["users"] as &$user) {
            $newslikes = explode(":", $user["newslikes"]);
            foreach ($newslikes as $key => &$newslike) {
                if ($id == $newslike) {
                    unset($newslikes[$key]);
                    break;
                }
            }
            $newsdislikes = explode(":", $user["newsdislikes"]);
            foreach ($newsdislikes as $key => &$newsdislike) {
                if ($id == $newsdislike) {
                    unset($newsdislikes[$key]);
                    break;
                }
            }
            $user["newslikes"] = implode(":", $newslikes);
            $user["newsdislikes"] = implode(":", $newsdislikes);
        }
        file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
    }
    if ($type == "cars") {
        $users = load_users("../data/users.json");
        foreach ($users["users"] as &$user) {
            $carlikes = explode(":", $user["carlikes"]);
            foreach ($carlikes as $key => &$carslike) {
                var_dump($carlikes);
                if($id == $carslike){
                    unset($carlikes[$key]);
                    break;
                }
            }
            $cardislikes = explode(":", $user["cardislikes"]);
            foreach($cardislikes as $key => &$carsdislike){
                if($id == $carsdislike){
                    unset($cardislikes[$key]);
                    break;
                }
            }
            $user["carlikes"] = implode(":", $carlikes);
            $user["cardislikes"] = implode(":", $cardislikes);
        }
        file_put_contents("../data/users.json", json_encode($users, JSON_PRETTY_PRINT));
    }
}

function delete_user_likes($user){
    $news_json = file_get_contents("data/news.json");
    $news = json_decode($news_json, true);
    $cars_json = file_get_contents("data/cars.json");
    $cars = json_decode($cars_json, true);

    $user_news_likes = explode(":", $user["newslikes"]);
    $user_news_dislikes = explode(":", $user["newsdislikes"]);
    $user_cars_likes = explode(":", $user["carlikes"]);
    $user_cars_dislikes = explode(":", $user["cardislikes"]);

    foreach($news["news"] as &$n){
        foreach($user_news_likes as $user_news_like){
            if($n["id"] == $user_news_like){
                $n["likes"]--;
            }
        }
        foreach($user_news_dislikes as $user_news_dislike){
            if($n["id"] == $user_news_dislike){
                $n["dislikes"]--;
            }
        }
    }
    foreach($cars["cars"] as &$car){
        foreach($user_cars_likes as $user_cars_like){
            if($car["id"] == $user_cars_like){
                $car["likes"]--;
            }
        }
        foreach($user_cars_dislikes as $user_cars_dislike){
            if($car["id"] == $user_cars_dislike){
                $car["dislikes"]--;
            }
        }
    }
    file_put_contents("data/news.json", json_encode($news, JSON_PRETTY_PRINT));
    file_put_contents("data/cars.json", json_encode($cars, JSON_PRETTY_PRINT));
}

function delete_user_cars($user){
    $cars_json = file_get_contents("data/cars.json");
    $cars = json_decode($cars_json, true);
    foreach($cars["cars"] as $car){
        if($car["sellerusername"] == $user["username"]){
            remove_cars_of_user($car["id"]);
        }
    }
}
function delete_user_pfp($user){
    $filename = strtolower($user["username"]);
    $full_file_name = "img/pfp/" . $filename;
    $success = false;
    if(file_exists($full_file_name . ".jpg")){
        unlink("img/pfp/" . $filename . ".jpg");
        $success = true;
    }
    if(file_exists($full_file_name . ".jpeg")){
        unlink("img/pfp/" . $filename . ".jpeg");
        $success = true;
    }
    if(file_exists($full_file_name . ".png")){
        unlink("img/pfp/" . $filename . ".png");
        $success = true;
    }
}


