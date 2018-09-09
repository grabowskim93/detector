<?php

$content = file_get_contents('http://192.168.43.236/');

$db = new PDO("mysql:host=localhost;dbname=detector", "root", "maja");

$statement = $db->prepare('SELECT id, mac_address FROM sensors where ip_address = :ip');
$result = $statement->execute(array(':ip' => '192.168.43.236'));

$id = $statement->fetch(PDO::FETCH_ASSOC);

$humRegex = '/Humidity.*: (.*)/m';
preg_match($humRegex, $content, $humMatch);
$tempRegex = '/Temperature.*: (.*)/m';
preg_match($tempRegex, $content, $tempMatch);

$hum = $humMatch[1];
$temp = $tempMatch[1];


$createdAt = date('Y-m-d H:i:s', time());

try {
    $statement2 = $db->prepare('INSERT INTO sensor_data (sensor_id, mac_address, temp, hum, created_at) VALUES (:sensor_id, :mac_address, :temp, :hum, :created_at)');
    $statement2->bindParam(':sensor_id', $id['id']);
    $statement2->bindParam(':mac_address', $id['mac_address']);
    $statement2->bindParam(':temp', $temp);
    $statement2->bindParam(':hum', $hum);
    $statement2->bindParam(':created_at', $createdAt);
    $statement2->execute();

} catch (Exception $e){
    var_dump($e->getMessage());
}
