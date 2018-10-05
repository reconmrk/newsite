<?php

/**
 * Created by PhpStorm.
 * User: konrad
 * Date: 30.03.2017
 * Time: 12:56
 */
class TicketUser
{
    protected $name;
    protected $uuid;

    function __construct($name, $uuid)
    {
        $this->name = $name;
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}