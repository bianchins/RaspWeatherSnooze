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

//With '/' at the end, directory of application
define('_APPLICATION_DIR','/var/www/');

/*
 * Php Timezone
 * See http://php.net/manual/en/timezones.php
 */
define('_TIME_ZONE','Europe/Rome');

/* 
 * Espeak voice: 
 * m1, m2, ... m8 male voices
 * f1, f2, f3, f4 female voices
 */
define('_ESPEAK_VOICE','m2');

//Language of text-to-speech
define('_ESPEAK_LANGUAGE','it');

//Amplitude (espeak -a <amplitude>)
define('_ESPEAK_AMPLITUDE','100');

//How many times the alarm should speak to you about weather (i reccomend 2 times, during the first i'm sleeping yet...)
define('_ESPEAK_TIMES','2');

//I want the weather for this city
define('_WEATHER_CITY','Rimini, Italy');

$_LOCALE_CONDITIONS = array(
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
    "26"=>      "nuvoloso",
    "27"=> 	"Sereno",
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
    "3200"=>    "non disponibile"
);