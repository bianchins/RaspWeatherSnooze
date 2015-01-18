<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function degToCompass($num) {
    $val=floor(($num/22.5)+.5);
    $arr=["N","NNE","NE","ENE","E","ESE", "SE", "SSE","S","SSW","SW","WSW","W","WNW","NW","NNW"];
    return $arr[($val % 16)];
}

$icone_condizioni = array(
"0"=> 	"wi-tornado",
"1"=> 	"wi-rain-wind",
"2"=> 	"wi-hurricane",
"3"=> 	"wi-thunderstorm",
"4"=> 	"wi-thunderstorm",
"5"=> 	"wi-rain-mix",
"6"=> 	"wi-rain-mix",
"7"=> 	"wi-snow",
"8"=> 	"wi-rain-mix",
"9"=> 	"wi-showers",
"10"=> 	"wi-rain-mix",
"11"=> 	"wi-showers",
"12"=> 	"wi-showers",
"13"=> 	"wi-snow",
"14"=> 	"wi-snow",
"15"=> 	"wi-snow",
"16"=> 	"wi-snow",
"17"=> 	"wi-hail",
"18"=> 	"wi-snow",
"19"=> 	"wi-dust",
"20"=> 	"wi-fog",
"21"=> 	"wi-fog",
"22"=> 	"wi-smoke",
"23"=> 	"wi-strong-wind",
"24"=> 	"wi-strong-wind",
"25"=> 	"wi-snowflake-cold",
"26"=>  "wi-cloudy",
"27"=> 	"wi-night-cloudy",
"28"=> 	"wi-day-cloudy",
"29"=> 	"wi-night-partly-cloudy",
"30"=> 	"wi-day-sunny-overcast",
"31"=> 	"wi-night-clear",
"32"=> 	"wi-day-sunny",
"33"=> 	"wi-night-clear",
"34"=> 	"wi-day-sunny",
"35"=> 	"wi-hail",
"36"=> 	"wi-hot",
"37"=> 	"wi-day-storm-showers",
"38"=> 	"wi-thunderstorm",
"39"=> 	"wi-thunderstorm",
"40"=> 	"wi-showers",
"41"=> 	"wi-snow-wind",
"42"=> 	"wi-snow-wind",
"43"=> 	"wi-snow-wind",
"44"=> 	"wi-cloud",
"45"=> 	"wi-storm-showers",
"46"=> 	"wi-snow",
"47"=> 	"wi-storm-showers",
"3200"=> "wi-stars"
    );


$condizioni = array(
"0"=> 	"tornado",
"1"=> 	"tempesta tropicale",
"2"=> 	"uragano",
"3"=> 	"forti temporali",
"4"=> 	"temporali",
"5"=> 	"pioggia mista a neve",
"6"=> 	"pioggia mista a nevischio",
"7"=> 	"neve mista a nevischio",
"8"=> 	"pioviggine gelata",
"9"=> 	"pioggerella",
"10"=> 	"pioggia gelata",
"11"=> 	"rovesci",
"12"=> 	"rovesci",
"13"=> 	"raffiche di neve",
"14"=> 	"rovesci di neve leggeri",
"15"=> 	"soffia neve",
"16"=> 	"neve",
"17"=> 	"grandinare",
"18"=> 	"nevischio",
"19"=> 	"polvere",
"20"=> 	"nebbioso",
"21"=> 	"foschia",
"22"=> 	"fumoso",
"23"=> 	"ventoso",
"24"=> 	"ventoso",
"25"=> 	"freddo",
"26"=>  "nuvoloso",
"27"=> 	"sereno",
"28"=> 	"Sereno",
"29"=> 	"parzialmente nuvoloso",
"30"=> 	"parzialmente nuvoloso",
"31"=> 	"Sereno",
"32"=> 	"soleggiato",
"33"=> 	"Sereno",
"34"=> 	"Sereno",
"35"=> 	"pioggia mista e grandine",
"36"=> 	"caldo",
"37"=> 	"isolati temporali",
"38"=> 	"temporali sparsi",
"39"=> 	"temporali sparsi",
"40"=> 	"Rovesci sparsi",
"41"=> 	"tormenta di neve",
"42"=> 	"rovesci di neve sparsi",
"43"=> 	"tormenta di neve",
"44"=> 	"parzialmente nuvoloso",
"45"=> 	"rovesci temporaleschi",
"46"=> 	"rovesci di neve",
"47"=> 	"Temporali isolati",
"3200"=> "non disponibile"
    );

    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";

    $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="Rimini, Italy") and u="c"';
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);
    // Convert JSON to PHP object
    $phpObj =  json_decode($json);
    //var_dump($phpObj);

    $weather = new stdClass();
    $weather->temp = $phpObj->query->results->channel->item->condition->temp;
    $weather->condition = $phpObj->query->results->channel->item->condition->code;
    $weather->condition_text = $condizioni[$phpObj->query->results->channel->item->condition->code];
    $weather->condition_original_text = $phpObj->query->results->channel->item->condition->text;
    $weather->sunrise = $phpObj->query->results->channel->astronomy->sunrise;
    $weather->sunset = $phpObj->query->results->channel->astronomy->sunset;
    $weather->humidity = $phpObj->query->results->channel->atmosphere->humidity;
    $weather->wind_speed = $phpObj->query->results->channel->wind->speed;
    $weather->wind_direction = degToCompass($phpObj->query->results->channel->wind->direction);
    $weather->wind_direction_deg = $phpObj->query->results->channel->wind->direction;
    $weather->forecast_code = $phpObj->query->results->channel->item->forecast[0]->code;
    $weather->forecast_high_temp = $phpObj->query->results->channel->item->forecast[0]->high;
    $weather->forecast_low_temp = $phpObj->query->results->channel->item->forecast[0]->low;
    $weather->forecast_icon = $icone_condizioni[$weather->forecast_code];
    
    $weather->forecast = $phpObj->query->results->channel->item->forecast;
    foreach($weather->forecast as &$forecast) {
        $forecast->icon = $icone_condizioni[$forecast->code];
    }
    
    
    echo json_encode($weather);
    
    //echo "\nMeteo per Rimini\n";
    //echo "----------------\n";
    //echo "Temperatura:      ".$phpObj->query->results->channel->item->condition->temp."° C\n";
    //echo "Condizioni meteo: ".$condizioni[$phpObj->query->results->channel->item->condition->code]."\n";
    //echo "Alba:             ".$phpObj->query->results->channel->astronomy->sunrise."\n";
    //echo "Tramonto:         ".$phpObj->query->results->channel->astronomy->sunset."\n";
    //echo "Umidità:          ".$phpObj->query->results->channel->atmosphere->humidity."%\n";
    //echo "Pressione:        ".$phpObj->query->results->channel->atmosphere->pressure." millibar\n";
    //echo "Previsioni:       ".$condizioni[$phpObj->query->results->channel->item->forecast[0]->code].", t. max ".$phpObj->query->results->channel->item->forecast[0]->high."° C, t. min ".$phpObj->query->results->channel->item->forecast[0]->low." °C \n";
    //date, high, low, code, day
    //echo "Vento:            ".$phpObj->query->results->channel->wind->speed." km/h ".degToCompass($phpObj->query->results->channel->wind->direction)."\n";
    //speed, chill, direction