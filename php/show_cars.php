<?php
function show_cars_on_index(){
    $cars = file_get_contents('data/cars.json');
    $cars_array = json_decode($cars, true);
    foreach($cars_array["cars"] as $car){
        if($car["approved"]) {
            if($car["usermade"]){
                echo('<div class="termekbox usermade">');
            }else {
                echo('<div class="termekbox">');
            }
            echo('<img src="' . $car["imgpath"] . '" alt="Eladó autó">');
            echo('<h2>' . $car["marka"] . ' ' . $car["tipus"] . ' ' . $car["evjarat"] . '</h2>');
            if($car["usermade"]){
                echo('<p>Felhasználó által kínált</p>');
            }else {
                echo('<p>' . $car["rovleiras"] . '</p>');
            }
            echo('<span class="price">' . $car["ar"] . ' HUF</span>');
            echo('<a class="termek-button" href="auto/auto-' . str_replace(' ', '', strtolower($car["marka"])) . '-' . str_replace(' ', '', strtolower($car["tipus"])) . '.php">Megtekintés</a>');
            echo('</div>');
        }
    }
}

function show_cars_on_car_page(){
    $cars = file_get_contents('../data/cars.json');
    $cars_array = json_decode($cars, true);
    foreach($cars_array["cars"] as $car){
        if(basename($_SERVER["REQUEST_URI"]) === 'auto-' . str_replace(' ', '', strtolower($car["marka"])) . '-' . str_replace(' ', '', strtolower($car["tipus"])) .'.php'){
            echo('<div class="termekbox" id="on">');
            echo('<img src="../' . $car["imgpath"] . '" alt="Eladó autó">');
            echo('<h2>' . $car["marka"] . ' ' . $car["tipus"] . ' ' . $car["evjarat"] . '</h2>');
            echo('<span class="price">' . $car["ar"] . ' HUF</span>');
        }else{
            if($car["approved"]) {
                if($car["usermade"]){
                    echo('<div class="termekbox usermade">');
                }else {
                    echo('<div class="termekbox">');
                }
                echo('<img src="' . $car["imgpath"] . '" alt="Eladó autó">');
                echo('<h2>' . $car["marka"] . ' ' . $car["tipus"] . ' ' . $car["evjarat"] . '</h2>');
                if($car["usermade"]){
                    echo('<p>Felhasználó által kínált</p>');
                }else {
                    echo('<p>' . $car["rovleiras"] . '</p>');
                }
                echo('<span class="price">' . $car["ar"] . ' HUF</span>');
                echo('<a class="termek-button" href="auto-' . str_replace(' ', '', strtolower($car["marka"])) . '-' . str_replace(' ', '', strtolower($car["tipus"])) . '.php">Megtekintés</a>');
            }
        }
        echo('</div>');
    }
}

function show_cars_on_admin_page(){
    $cars = file_get_contents('data/cars.json');
    $cars_array = json_decode($cars, true);
    $exists = false;
    foreach($cars_array["cars"] as $car){
        if(!$car["approved"]){
            $exists = true;
            echo('<div class="termekbox">');
            echo('<img src="' . $car["imgpath"] . '" alt="Eladó autó">');
            echo('<h2>' . $car["marka"] . ' ' . $car["tipus"] . ' ' . $car["evjarat"] . '</h2>');
            echo('<p>' . $car["rovleiras"] . '</p>');
            echo('<span class="price">' . $car["ar"] . ' HUF</span>');
            echo('<a class="termek-button" href="auto/auto-' . str_replace(' ', '', strtolower($car["marka"])) . '-' . str_replace(' ', '', strtolower($car["tipus"])) . '.php">Megtekintés</a>');
            echo('<form method="post">');
            echo('<input type="hidden" name="car_id" value="' . $car["id"] . '">');
            echo('<button class="termek-button" type="submit" name="approvecar">Jóváhagyás</button>');
            echo('</form>');
            echo("</div>");
        }
    }
    if(!$exists){
        echo('<h2>Jelenleg nincs jóváhagyásra váró autó!</h2>');
    }
}
