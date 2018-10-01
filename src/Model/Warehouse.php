<?php

namespace App\Model;

class Warehouse
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $address;
    /**
     * @var int
     */
    private $user_id;
    /**
     * @var int
     */
    private $capacity;

    public function __construct($id, $address,  $capacity, $user_id)
    {
        $this->id = $id;
        $this->address = $address;
        $this->user_id = $user_id;
        $this->capacity = $capacity;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($id_user)
    {
        $this->user_id = $id_user;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }
}