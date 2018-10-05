<?php

/**
 * Created by PhpStorm.
 * User: konrad
 * Date: 17.03.2017
 * Time: 23:15
 */
class Group
{
    private $permissions;
    private $name;


    function __construct($name, $permissions)
    {
        $this->name = $name;
        $this->permissions = $permissions;
    }


    function getPermissions() {
        return $this->permissions;
    }

    function hasPermission($permission) {
        foreach ($this->permissions as $li => $item) {
            if($item == 'administrator') {
                return true;
            }
        }
        foreach ($this->permissions as $li => $item) {
            if($item == $permission) {
                return true;
            }
        }
        return false;
    }

    function getName() {
        return $this->name;
    }

}