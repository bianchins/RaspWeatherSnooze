<?php
/* 
 *  .oPYo.                      o      o                 o  8                   .oPYo.                                   
 *  8   `8                      8      8                 8  8                   8                                        
 * o8YooP' .oPYo. .oPYo. .oPYo. 8      8 .oPYo. .oPYo.  o8P 8oPYo. .oPYo. oPYo. `Yooo. odYo. .oPYo. .oPYo. .oooo. .oPYo. 
 *  8   `b .oooo8 Yb..   8    8 8  db  8 8oooo8 .oooo8   8  8    8 8oooo8 8  `'     `8 8' `8 8    8 8    8   .dP  8oooo8 
 *  8    8 8    8   'Yb. 8    8 `b.PY.d' 8.     8    8   8  8    8 8.     8          8 8   8 8    8 8    8  oP'   8.     
 *  8    8 `YooP8 `YooP' 8YooP'  `8  8'  `Yooo' `YooP8   8  8    8 `Yooo' 8     `YooP' 8   8 `YooP' `YooP' `Yooo' `Yooo' 
 * :..:::..:.....::.....:8 ....:::..:..:::.....::.....:::..:..:::..:.....:..:::::.....:..::..:.....::.....::.....::.....:
 * ::::::::::::::::::::::8 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 * ::::::::::::::::::::::..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 */

class Weather {

    function degToCompass($num) {
        $val=floor(($num/22.5)+.5);
        $arr=["N","NNE","NE","ENE","E","ESE", "SE", "SSE","S","SSW","SW","WSW","W","WNW","NW","NNW"];
        return $arr[($val % 16)];
    }

    function collectWeatherData($city, $_LOCALE_CONDITIONS) {
        
        $BASE_URL = "http://query.yahooapis.com/v1/public/yql";

        $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="'.$city.'") and u="c"';
        $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

        // Make call with cURL
        $session = curl_init($yql_query_url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        $json = curl_exec($session);
        // Convert JSON to PHP object
        $phpObj =  json_decode($json);

        $weather = new stdClass();
        $weather->temp = $phpObj->query->results->channel->item->condition->temp;
        $weather->condition = $phpObj->query->results->channel->item->condition->code;
        $weather->condition_text = $_LOCALE_CONDITIONS[$phpObj->query->results->channel->item->condition->code];
        $weather->condition_original_text = $phpObj->query->results->channel->item->condition->text;
        $weather->sunrise = $phpObj->query->results->channel->astronomy->sunrise;
        $weather->sunset = $phpObj->query->results->channel->astronomy->sunset;
        $weather->humidity = $phpObj->query->results->channel->atmosphere->humidity;
        $weather->wind_speed = $phpObj->query->results->channel->wind->speed;
        $weather->wind_direction = Weather::degToCompass($phpObj->query->results->channel->wind->direction);
        $weather->wind_direction_deg = $phpObj->query->results->channel->wind->direction;
        $weather->forecast_code = $phpObj->query->results->channel->item->forecast[0]->code;
        $weather->forecast_condition = $_LOCALE_CONDITIONS[$phpObj->query->results->channel->item->forecast[0]->code];
        $weather->forecast_high_temp = $phpObj->query->results->channel->item->forecast[0]->high;
        $weather->forecast_low_temp = $phpObj->query->results->channel->item->forecast[0]->low;

        return $weather;
    }
}