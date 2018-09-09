<?php
namespace App\Core;

use Illuminate\Database\Capsule\Manager;

/**
 * DB manager class
 */
class DB extends Manager
{
    /**
     * Dataset db prefix
     * @var string
     */
    protected $datasetDatabasePrefix;

    /**
     * Switch dataset 
     * @param  integer $datasetId
     */
    public static function switchDataset($datasetId)
    {   
        static::$instance->switchDatabaseName('dataset', $datasetId);
    }

    /**
     * Switch database name for given connection
     * @param string $connection
     * @param string $datasetId 
     */
    public function switchDatabaseName($connection, $datasetId)
    {
        $config = require CONFIG_PATH;
        $config = $config['dataset'];
        $config['database'] = $this->datasetDatabasePrefix.$datasetId;

        // addConnection doesnt return anything
        $this->addConnection($config, $connection);
        $dbManager = $this->getDatabaseManager();
        $dbManager->purge('dataset');
        $dbManager->connection('dataset');
        //$reconnectResult = static::reconnect('dataset');

        //ob_start();
        //var_dump($reconnectResult);
        //$reconnectResult = ob_get_clean();
        /*
        $backtrace = debug_backtrace();
        $file = print_r(array_column($backtrace, 'class'), true);

        $string = date('Y-m-d H:i:s').' '
            ."File: ".$file."\n"
            .'Connection name: '.$connection."\n"
            ."Dataset ID: ".$datasetId."\n"
            //."Reconnect result: ".print_r($reconnectResult, true)."\n"
            ."Config: \n".print_r($config, true)."\n\n";

        file_put_contents('db.log', $string, FILE_APPEND);
        */
    }

    /**
     * Set dataset database prefix
     * @param string $prefix
     * @return $this
     */
    public function setDatasetDatabasePrefix($prefix)
    {
        $this->datasetDatabasePrefix = $prefix;

        return $this;
    }
}