<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 13.09.2018
 * Time: 20:56
 */

namespace App\Model;


class Item
{
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $size;
    private $type;
    private $warehouse_id;
    public function __construct($id, $name, $price, $quantity, $size, $type, $id_warehouse)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->size = $size;
        $this->type = $type;
        $this->warehouse_id = $id_warehouse;
    }
    public function copyFromArray($array)
    {
        $this->id = $array['id'];
        $this->name = $array['name'];
        $this->price = $array['price'];
        $this->quantity = $array['quantity'];
        $this->size = $array['size'];
        $this->type = $array['type'];
        $this->warehouse_id = $array['id_warehouse'];
    }
    public function copyFromItem($item)
    {
        $this->id = $item->getId();
        $this->name = $item->getName();
        $this->price = $item->getPrice();
        $this->quantity = $item->getQuantity();
        $this->size = $item->getSize();
        $this->type = $item->getType();
        $this->warehouse_id = $item->getWarehouseId();
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        return $this->name = $name;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function getSumPrice()
    {
        return $this->quantity*$this->price;
    }
    public function getSize()
    {
        return $this->size;
    }
    public function setSize($size)
    {
        $this->size = $size;
    }
    public function getSumSize()
    {
        return $this->size*$this->quantity;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function getWarehouseId()
    {
        return $this->warehouse_id;
    }
    public function setWarehouseId($id_warehouse)
    {
        $this->warehouse_id = $id_warehouse;
    }
}