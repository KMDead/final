<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 13.09.2018
 * Time: 20:59
 */

namespace App\Model;

class Transfer
{
    private $id;
    private $date;
    private $is_foreign;
    private $type;
    private $warehouse_id;
    private $foreign_transfer_name;
    private $items = [];
    public function __construct($id, $date, $is_foreign, $type, $warehouse_id, $foreign_transfer_name, $items)
    {
        $this->id = $id;
        $this->warehouse_id = $warehouse_id;
        $this->date = $date;
        $this->is_foreign = $is_foreign;
        $this->type = $type;
        $this->foreign_transfer_name = $foreign_transfer_name;
        $this->items = $items;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function Is_foreign()
    {
        return $this->is_foreign;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getWarehouseId()
    {
        return $this->warehouse_id;
    }
    public function getItems()
    {
        return $this->items;
    }
    public function getForeignName()
    {
        return $this->foreign_transfer_name;
    }
}