<?php


$db = new PDO("mysql:host=localhost;dbname=detector", "root", "maja");

$statement = $db->prepare('SELECT id, mac_address, ip_address FROM sensors');
$result = $statement->execute();

$sensors = $statement->fetch(PDO::FETCH_ASSOC);

foreach ($sensors as $sensor) {
    $content = file_get_contents('http://' . $sensor['ip_address']);

    $humRegex = '/Humidity.*: (.*)/m';
    preg_match($humRegex, $content, $humMatch);
    $tempRegex = '/Temperature.*: (.*)/m';
    preg_match($tempRegex, $content, $tempMatch);

    $hum = $humMatch[1];
    $temp = $tempMatch[1];


    $createdAt = date('Y-m-d H:i:s', time());

    try {
        $statement2 = $db->prepare('INSERT INTO sensor_data (sensor_id, mac_address, temp, hum, created_at) VALUES (:sensor_id, :mac_address, :temp, :hum, :created_at)');
        $statement2->bindParam(':sensor_id', $sensor['id']);
        $statement2->bindParam(':mac_address', $sensor['mac_address']);
        $statement2->bindParam(':temp', $temp);
        $statement2->bindParam(':hum', $hum);
        $statement2->bindParam(':created_at', $createdAt);
        $statement2->execute();

    } catch (Exception $e){
        var_dump($e->getMessage());
    }
}
