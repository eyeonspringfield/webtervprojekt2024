<?php
include_once "user_manager.php";
function admin_delete_appointments($date){

    $users = json_decode(file_get_contents("data/users.json"), true);
    foreach($users["users"] as &$user){
        $appointments = explode("|", $user["appointments"]);
        foreach($appointments as $appkey => &$appointment){
            if(strpos($appointment, ":" . $date) !== false){
                unset($appointments[$appkey]);
            }
        }
        $user["appointments"] = implode("|", $appointments);
    }
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
}

function addAppointment($username,$carName,$date){
    $appointmentNEW = $carName . ":" . $date;
    $letezik = false;

    $maiDatum = date('Y-m-d');

    if ($date >= $maiDatum && date('l', strtotime($date)) !== "Sunday"){
        $jsonData = file_get_contents('../data/users.json');
        $users = json_decode($jsonData, true);

        foreach ($users['users'] as &$user) {
            if ($user['username'] === $username) {
                $appointments = explode('|', $user['appointments']);
                foreach ($appointments as $existingAppointment) {
                    if ($existingAppointment === $appointmentNEW) {
                        $letezik = true;
                        break;
                    }
                }
                if (!$letezik && $user["appointments"] != ""){
                    $user['appointments'] .= '|' . $appointmentNEW;
                } else if(!$letezik){
                   $user["appointments"] = $appointmentNEW;
                }else{
                    echo "Nem lehet vasárnapra időpontot foglalni!";
                }
            }
        }

        unset($user);
        foreach($users["users"] as $user){
            if($user["username"] == $username){
                $_SESSION["user"] = $user;
            }
        }

        $updatedJsonData = json_encode($users, JSON_PRETTY_PRINT);

        file_put_contents('../data/users.json', $updatedJsonData);
    }
}

function deleteAppointment($username, $data) {
    $jsonData = file_get_contents('data/users.json');
    $users = json_decode($jsonData, true);
foreach($data as $appointment_to_delete) {
    foreach ($users['users'] as &$user) {
        if ($user['username'] === $username) {
            $appointments = explode('|', $user['appointments']);
            $updatedAppointments = [];
            foreach ($appointments as $appointment) {
                if (strtolower(str_replace(" ", "", str_replace(":", "", str_replace("-", "", $appointment)))) !== $appointment_to_delete) {
                    $updatedAppointments[] = $appointment;
                }
            }
            $user['appointments'] = implode('|', $updatedAppointments);
            break;
        }
    }
}
    unset($user);
    foreach($users["users"] as $user){
        if($user["username"] == $username){
            $_SESSION["user"] = $user;
        }
    }
    $updatedJsonData = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents('data/users.json', $updatedJsonData);
}



function load_appointments_on_profile($session){
    if($session["user"]["appointments"] === ""){
        echo '<div class="idopont">';
        echo '<h3>Önnek nincs jelenleg foglalt időpontja!</h3>';
        echo '</div>';
        echo '<br>';
    }else{
        $appointments = $session["user"]["appointments"];
        $pairs = explode("|", $appointments);
        foreach ($pairs as $pair) {
            list($key, $value) = explode(":", $pair);
            echo '<div class="idopont">';
            echo '<h3>' . $key . '</h3>';
            echo '<h4>' . $value . '</h4>';
            echo '<input type="checkbox" name="appointments[]" value="' . strtolower(str_replace(" ", "", str_replace("-", "", $key))) . str_replace("-", "", $value) . '">';
            echo '</div>';
            echo '<br>';

        }
        echo '<input type="submit" class="submiter" name="deleteapp" value="Időpont(ok) törlése">';
    }

}
