<?php

namespace App\Sensors\Controllers;

use App\Core\Controller;
use App\Sensors\Sensor;
use App\Sensors\SensorData;
use Slim\Http\Request;
use Slim\Http\Response;

class SensorController extends Controller
{

    public function scan(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {

        $data = [];
        $cmd = "nmap -p 80 192.168.43.0-255";
        $output = shell_exec($cmd);

        $re = '/Nmap scan report for (.*)/m';

        preg_match_all($re, $output, $matches);

        if (empty($matches[1])) {
            $data['info'] = 'No available sensors';
            return $this->render('scan.html.twig', $data);
        }

        $re2 = "/80\/tcp (.*)/m";
        preg_match_all($re2, $output, $matches2);

        foreach ($matches[1] as $key => $ip) {
            $ipRegex = '/\((.*)\)/m';
            preg_match($ipRegex, $ip, $ipAddress);
            if (filter_var($ipAddress[1], FILTER_VALIDATE_IP)) {
                if (str_contains($matches2[1][$key], 'open') && $ipAddress[1] != getHostByName(getHostName())) {
                    $macAddress = $this->getMacAddress($ipAddress[1]);
                    $data['sensors'][$key]['ip'] = $ipAddress[1];
                    $data['sensors'][$key]['mac'] = $macAddress;

                    if ($this->checkIfSensorExist($macAddress)) {
                        $data['sensors'][$key]['add'] = false;
                        $data['sensors'][$key]['id'] = $this->checkIfSensorExist($macAddress);
                        $data['sensors'][$key]['type'] = $this->getSensorType($macAddress);
                    } else {
                        $data['sensors'][$key]['add'] = true;
                        $data['sensors'][$key]['type'] = $this->checkSensorType(file_get_contents('http://' . $ipAddress[1]));
                    }
                }
            }
        }

        //Fake data to tests
//        $data['sensors'][1]['ip'] = '192.168.1.111';
//        $data['sensors'][1]['mac'] = 'aa:ff:aa:bb';
//        $data['sensors'][1]['add'] = true;
//        $data['sensors'][1]['type'] = 'Temperature & Humidity Sensor';


        $data['info'] = 'Detector found: ' . count($data['sensors']) . ' active sensor(s)';


        return $this->render('scan.html.twig', $data);
    }

    private function getMacAddress($ip)
    {
        $cmd = "arp " . $ip;
        $output = shell_exec($cmd);

        $regex= '/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/';
        preg_match($regex, $output, $match);

        return $match[0];
    }

    private function checkIfSensorExist($macAddress)
    {
        $sensor = Sensor::where('mac_address', '=', $macAddress)
            ->first();

        if ($sensor) {
            $sensor = $sensor->toArray();
        }

        return $sensor['id'];
    }

    private function getSensorType($macAddress)
    {
        $sensor = Sensor::where('mac_address', '=', $macAddress)->first();

        return $sensor['type'];
    }

    public function addSensor(Request $request, Response $response, $args)
    {
        $number = Sensor::all()->count();

        $params = $request->getParams();

        $sensor = new Sensor();
        $sensor->ip_address = $params['ip_address'];
        $sensor->mac_address = $params['mac_address'];
        $sensor->type = $params['type'];
        $sensor->name = 'Sensor#' . ($number + 1);
        $sensor->save();

        $sensor = $sensor->toArray();
        $sensor['response'] = file_get_contents('http://' . $params['ip_address']);
        //hardcoded test data
//        $sensor['response'] = '<h1>ESP temp Sensor:</h1>
//<p> Temp: 24 C</p>';

        return $this->render('details.html.twig', $sensor);
    }

    private function getSensors()
    {
        return Sensor::all()->toArray();
    }

    public function sensorsList()
    {
        $sensors = $this->getSensors();
        return $this->render('list.html.twig', ['sensors' => $sensors]);

    }

    public function getDetails(Request $request, Response $response, $args)
    {
        $id = (int) $args['id'];

        $sensor = Sensor::find($id)->toArray();

        $sensor['response'] = file_get_contents('http://' . $sensor['ip_address']);
        //hardcoded test data
//            $sensor['response'] = 'TEST';
//            $sensor['response'] = '<h1>ESP temp Sensor:</h1>
//<p> Temp: 24 C</p>';

        return $this->render('details.html.twig', $sensor);
    }

    private function checkSensorType($content)
    {
        if (str_contains($content, 'Temperature') && str_contains($content, 'Humidity')) {
            return 'Temperature & Humidity Sensor';
        }

        return 'Unknown sensor type';
    }

    public function updateSensor(Request $request, Response $response, $args)
    {
        $id = (int) $args['id'];
        $params = $request->getParams();

        $sensor = Sensor::find($id);

        $sensor->type = $params['type'];
        $sensor->name = $params['name'];
        $sensor->save();

        $sensor = $sensor->toArray();

        $sensor['response'] = file_get_contents('http://' . $sensor['ip_address']);

        return $this->render('details.html.twig', $sensor);
    }

    public function sensorData(Request $request, Response $response, $args)
    {
        $mac = (int) $args['mac'];
        $sensorData = SensorData::where('mac_address', $mac)
            ->limit(15)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        $labels = array_column($sensorData, 'created_at');
        $series = array_column($sensorData, 'temp');

        print_r(json_encode(['labels' => $labels, 'series' => $series]));
    }
}
