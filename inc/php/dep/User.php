<?php

/**
 * Created by PhpStorm.
 * User: konrad
 * Date: 12.03.2017
 * Time: 14:49
 */
class User
{
    private $username;
    private $password;
    private $group;

    function __construct($username, $password, $group)
    {
        $this->username = $username;
        $this->password = $password;
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

}