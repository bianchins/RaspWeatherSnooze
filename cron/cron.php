<?php
#!/usr/bin/php -q

require('config.php');
require('weather.php');

$db = new PDO('sqlite:'._APPLICATION_DIR.'api/db.sqlite3');

//Set timezone
date_default_timezone_set(_TIME_ZONE);

$days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

$sth = $db->prepare('SELECT count(*) FROM alarms WHERE hour = :hour AND '.$days[date('w')].'=1 LIMIT 1;');
$sth->bindParam(':hour', date('H:i'), PDO::PARAM_STR);
$sth->execute();

//Execute alarm ?
if($sth->fetchColumn()>0) {
    
    //Collect weather data
    $weather = Weather::collectWeatherData(_WEATHER_CITY, $_LOCALE_CONDITIONS);
    
    //Parse text to speech
    $parsed_text = readSetting($db, 'espeak_text');
    foreach($weather as $key => $value) {
        $parsed_text = str_replace('['.$key.']', $value, $parsed_text);       
    }
    
    //Execute alarm tone
    //exec('omxplayer -o local '.$tone);
    
    //Execute espeak
    exec('/usr/bin/espeak -w out.wav -v'._ESPEAK_LANGUAGE.'+'._ESPEAK_VOICE.' "'.$parsed_text.'" && aplay out.wav && rm out.wav');
}

//Future feature
function readSetting($db, $key) {
    $sth = $db->prepare('SELECT value FROM settings WHERE key = ? LIMIT 1;');
    $sth->execute([$key]);
    return $sth->fetchAll(PDO::FETCH_CLASS)[0]->value;
}