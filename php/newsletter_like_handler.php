<?php
include_once "user_manager.php";

function check_if_newsliked($usersession, $id): bool{
    $users = load_users("data/users.json");
    foreach($users["users"] as $user){
        if($usersession["username"] === $user["username"]) {
            $likes = explode(":", $user["newslikes"]);
            return array_search($id, $likes);
        }
    }
    return false;
}

function check_if_newsdisliked($usersession, $id): bool{
    $users = load_users("data/users.json");
    foreach($users["users"] as $user){
        if($usersession["username"] === $user["username"]) {
            $likes = explode(":", $user["newsdislikes"]);
            return array_search($id, $likes);
        }
    }
    return false;
}
function check_news_likes($usersession, $id){
    $users = load_users("data/users.json");
    foreach($users["users"] as &$user){
        if($usersession["username"] === $user["username"]){
            $likes = explode(":", $user["newslikes"]);
            $dislikes = explode(":", $user["newsdislikes"]);
            if(array_search($id, $likes)){
                $index = array_search($id, $likes);
                remove_news_like($id);
                unset($likes[$index]);
            }else{
                add_news_like($id);
                if(array_search($id, $dislikes)){
                    $index = array_search($id, $dislikes);
                    remove_news_dislike($id);
                    unset($dislikes[$index]);
                    $dislikes_string = implode(":", $dislikes);
                    $user["newsdislikes"] = $dislikes_string;
                }
                $likes[] = $id;
            }
            $likes_string = implode(":", $likes);
            $user["newslikes"] = $likes_string;
        }
    }
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function check_news_dislikes($usersession, $id){
    $users = load_users("data/users.json");
    foreach($users["users"] as &$user){
        if($usersession["username"] === $user["username"]){
            $dislikes = explode(":", $user["newsdislikes"]);
            $likes = explode(":", $user["newslikes"]);
            if(array_search($id, $dislikes)){
                $index = array_search($id, $dislikes);
                remove_news_dislike($id);
                unset($dislikes[$index]);
            }else{
                add_news_dislike($id);
                if(array_search($id, $likes)){
                    $index = array_search($id, $likes);
                    remove_news_like($id);
                    unset($likes[$index]);
                    $likes_string = implode(":", $likes);
                    $user["newslikes"] = $likes_string;
                }
                $dislikes[] = $id;
            }
            $dislikes_string = implode(":", $dislikes);
            $user["newsdislikes"] = $dislikes_string;
        }
    }
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function add_news_like($id){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $index = array_search($id, array_column($news_array["news"], "id"));
    if($index !== false) {
        $news_array["news"][$index]["likes"]++;
        $new_news_json = json_encode($news_array, JSON_PRETTY_PRINT);
        file_put_contents("data/news.json", $new_news_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}

function remove_news_like($id){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $index = array_search($id, array_column($news_array["news"], "id"));
    if($index !== false) {
        $news_array["news"][$index]["likes"]--;
        $new_news_json = json_encode($news_array, JSON_PRETTY_PRINT);
        file_put_contents("data/news.json", $new_news_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a likeot!</h1>");
    }
}

function add_news_dislike($id){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $index = array_search($id, array_column($news_array["news"], "id"));
    if($index !== false) {
        $news_array["news"][$index]["dislikes"]++;
        $new_news_json = json_encode($news_array, JSON_PRETTY_PRINT);
        file_put_contents("data/news.json", $new_news_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a dislikeot!</h1>");
    }
}

function remove_news_dislike($id){
    $news_json = file_get_contents("data/news.json");
    $news_array = json_decode($news_json, true);
    $index = array_search($id, array_column($news_array["news"], "id"));
    if($index !== false) {
        $news_array["news"][$index]["dislikes"]--;
        $new_news_json = json_encode($news_array, JSON_PRETTY_PRINT);
        file_put_contents("data/news.json", $new_news_json);
    }else{
        echo("<h1>Nem sikerült elmenteni a dislikeot!</h1>");
    }
}
