<?php

namespace App\Sensors;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    /**
     * Return holiday ID
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return holiday date
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return holiday comment
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * Return holiday comment
     * @return string
     */
    public function getMacAddress()
    {
        return $this->mac_address;
    }

    /**
     * Return created date/time
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Return updated date/time
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}