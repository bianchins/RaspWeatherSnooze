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

require 'vendor/autoload.php';
$app = new \Slim\Slim();
$app->contentType('application/json');
$db = new PDO('sqlite:/var/www/api/db.sqlite3');


function returnResult($action, $success = true, $id = 0)
{
    echo json_encode([
        'action' => $action,
        'success' => $success,
        'id' => intval($id),
    ]);
}

$app->get('/alarm', function () use ($db, $app) {
    $sth = $db->query('SELECT * FROM alarms;');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

$app->get('/alarm/:id', function ($id) use ($db, $app) {
    $sth = $db->prepare('SELECT * FROM alarms WHERE id = ? LIMIT 1;');
    $sth->execute([intval($id)]);
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS)[0]);
});

$app->get('/settings/:key', function ($key) use ($db, $app) {
    $sth = $db->prepare('SELECT * FROM settings WHERE key = ? LIMIT 1;');
    $sth->execute([$key]);
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS)[0]);
});

$app->put('/settings/:key', function ($key) use ($db, $app) {
    $sth = $db->prepare('UPDATE settings SET value = ? WHERE key = ? LIMIT 1;');
    $sth->execute([$app->request()->post('value'), $key]);
    returnResult('update', $sth->rowCount() == 1, $db->lastInsertId());
});

$app->post('/alarm', function () use ($db, $app) {
    $sth = $db->prepare('INSERT INTO alarms (hour, flag_weather, tones, monday, tuesday, wednesday, thursday, friday, saturday, sunday) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
    $sth->execute([
        $app->request()->post('hour'),
        $app->request()->post('flag_weather'),
        $app->request()->post('tones'),
        $app->request()->post('monday'),
        $app->request()->post('tuesday'),
        $app->request()->post('wednesday'),
        $app->request()->post('thursday'),
        $app->request()->post('friday'),
        $app->request()->post('saturday'),
        $app->request()->post('sunday'),
    ]);

    returnResult('add', $sth->rowCount() == 1, $db->lastInsertId());
});

$app->delete('/alarm/:id', function ($id) use ($db) {
    $sth = $db->prepare('DELETE FROM alarms WHERE id = ?;');
    $sth->execute([intval($id)]);

    returnResult('delete', $sth->rowCount() == 1, $id);
});

$app->get('/install', function () use ($db) {
    $db->exec('CREATE TABLE IF NOT EXISTS alarms (
                    id INTEGER PRIMARY KEY, 
                    hour TEXT, 
                    flag_weather INTEGER,
                    tones TEXT,
                    monday INTEGER,
                    tuesday INTEGER,
                    wednesday INTEGER,
                    thursday INTEGER,
                    friday INTEGER,
                    saturday INTEGER,
                    sunday INTEGER);');
    $db->exec('CREATE TABLE IF NOT EXISTS settings (
                    key TEXT PRIMARY KEY, 
                    value TEXT);');
    $db->exec('INSERT INTO settings(key, value) VALUES(\'espeak_language\',\'en\');');
    $db->exec('INSERT INTO settings(key, value) VALUES(\'espeak_text\',\'\');');
    $db->exec('INSERT INTO settings(key, value) VALUES(\'tone\',\'\');');
    $db->exec('INSERT INTO settings(key, value) VALUES(\'weather_city\',\'Rimini, Italy\');');
    
    returnResult('install');
});



$app->run();