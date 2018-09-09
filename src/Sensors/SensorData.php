<?php

namespace App\Sensors;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';

    /**
     * Return holiday ID
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSensorId()
    {
        return $this->sensor_id;
    }

    /**
     * Return created date/time
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
