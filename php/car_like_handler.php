<?php
include_once "user_manager.php";

function load_likes($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        echo('<div id="likedislike">');
        echo('<form method="post">');
        echo('<input type="hidden" name="like">');
        echo('<button type="submit" name="like"><img src="../img/likebutton.png" alt="like button" height="15"></button>');
        echo('<p id="likecount" class="likecount">' . $cars_array["cars"][$index]["likes"] . '</p>');
        echo('</form>');
        echo('<form method="post">');
        echo('<input type="hidden" name="dislikeid">');
        echo('<button type="submit" name="dislike"><img class="dislikeimg" src="../img/likebutton.png" alt="dislike button" height="15"></button>');
        echo('<p id="dislikecount" class="dislikecount">' . $cars_array["cars"][$index]["dislikes"] . '</p>');
        echo('</form>');
        echo('</div>');
    }
}

function load_likes_logged_in($usersession, $id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        echo('<div id="likedislike">');
        echo('<form method="post">');
        echo('<input type="hidden" name="like">');
        if(check_if_carsliked($usersession, $id)){
            echo('<button type="submit" name="like" class="liked"><img src="../img/likebutton.png" alt="like button" height="15"></button>');
        }else{
            echo('<button type="submit" name="like"><img src="../img/likebutton.png" alt="like button" height="15"></button>');
        }
        echo('<p id="likecount" class="likecount">' . $cars_array["cars"][$index]["likes"] . '</p>');
        echo('</form>');
        echo('<form method="post">');
        echo('<input type="hidden" name="dislikeid">');
        if(check_if_carsdisliked($usersession, $id)){
            echo('<button type="submit" name="dislike" class="liked dislikeimg"><img src="../img/likebutton.png" alt="like button" height="15"></button>');
        }else{
            echo('<button type="submit" name="dislike" class="dislikeimg"><img src="../img/likebutton.png" alt="like button" height="15"></button>');
        }
        echo('<p id="dislikecount" class="dislikecount">' . $cars_array["cars"][$index]["dislikes"] . '</p>');
        echo('</form>');
        echo('</div>');
    }
}

function check_if_carsliked($usersession, $id): bool{
    $users = load_users("../data/users.json");
    foreach($users["users"] as $user){
        if($usersession["username"] === $user["username"]) {
            $likes = explode(":", $user["carlikes"]);
            return array_search($id, $likes);
        }
    }
    return false;
}

function check_if_carsdisliked($usersession, $id): bool{
    $users = load_users("../data/users.json");
    foreach($users["users"] as $user){
        if($usersession["username"] === $user["username"]) {
            $dislikes = explode(":", $user["cardislikes"]);
            return array_search($id, $dislikes);
        }
    }
    return false;
}


function check_likes($usersession, $id){
    $users = load_users("../data/users.json");
    foreach($users["users"] as &$user){
        if($usersession["username"] === $user["username"]){
            $likes = explode(":", $user["carlikes"]);
            $dislikes = explode(":", $user["cardislikes"]);
            if(array_search($id, $likes)){
                $index = array_search($id, $likes);
                remove_like($id);
                unset($likes[$index]);
            }else{
                add_like($id);
                if(array_search($id, $dislikes)){
                    $index = array_search($id, $dislikes);
                    remove_dislike($id);
                    unset($dislikes[$index]);
                    $dislikes_string = implode(":", $dislikes);
                    $user["cardislikes"] = $dislikes_string;
                }
                $likes[] = $id;
            }
            $likes_string = implode(":", $likes);
            $user["carlikes"] = $likes_string;
        }
    }
    file_put_contents("../data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function check_dislikes($usersession, $id){
    $users = load_users("../data/users.json");
    foreach($users["users"] as &$user){
        if($usersession["username"] === $user["username"]){
            $dislikes = explode(":", $user["cardislikes"]);
            $likes = explode(":", $user["carlikes"]);
            if(array_search($id, $dislikes)){
                $index = array_search($id, $dislikes);
                remove_dislike($id);
                unset($dislikes[$index]);
            }else{
                add_dislike($id);
                if(array_search($id, $likes)){
                    $index = array_search($id, $likes);
                    remove_like($id);
                    unset($likes[$index]);
                    $likes_string = implode(":", $likes);
                    $user["carlikes"] = $likes_string;
                }
                $dislikes[] = $id;
            }
            $dislikes_string = implode(":", $dislikes);
            $user["cardislikes"] = $dislikes_string;
        }
    }
    file_put_contents("../data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function add_like($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        $cars_array["cars"][$index]["likes"]++;
        $new_cars_json = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("../data/cars.json", $new_cars_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}

function remove_like($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        $cars_array["cars"][$index]["likes"]--;
        $new_cars_json = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("../data/cars.json", $new_cars_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}

function add_dislike($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        $cars_array["cars"][$index]["dislikes"]++;
        $new_cars_json = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("../data/cars.json", $new_cars_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}

function remove_dislike($id){
    $cars_json = file_get_contents("../data/cars.json");
    $cars_array = json_decode($cars_json, true);
    $index = array_search($id, array_column($cars_array["cars"], "id"));
    if($index !== false) {
        $cars_array["cars"][$index]["dislikes"]--;
        $new_cars_json = json_encode($cars_array, JSON_PRETTY_PRINT);
        file_put_contents("../data/cars.json", $new_cars_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}
