<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 17.09.2018
 * Time: 19:16
 */

namespace App\Repository;
use App\Model\Item;
use App\Model\Transfer;
use App\Model\Warehouse;
use PHPUnit\Runner\Exception;

class ItemRepository extends AbstractRepository
{
    /**
     * @var int
    */
    public function __construct($dbConnection)
    {
        parent::__construct($dbConnection);
    }
    /**
     * @return Item[]
    */
    public function getAll()
    {
        $Items = [];
        $rows = $this->dbConnection->executeQuery("select Item.id, name, price, size, quantity, type, id_warehouse  from Item, Warehouse, Batch  
        where Item.id = Batch.id_item and Batch.id_warehouse = Warehouse.id and Warehouse.id_user = $this->id_user;");
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items[] = new Item($row['id'], $row['name'], $row['price'],
                $row['quantity'], $row['size'], $row['type'], $row['id_warehouse']);
        }
        return $Items;
    }
    public function getAllFromItem()
    {
        $Items = [];
        $rows = $this->dbConnection->executeQuery("select * from Item where User_id = $this->id_user;");
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items[] = new Item($row['id'], $row['name'], $row['price'],
                null, $row['size'], $row['type'], null);
        }
        return $Items;
    }

    public function getBy($str)
    {
        $Items = [];
        $rows = $this->dbConnection->executeQuery("select Item.id, name, price, size, quantity, type, id_warehouse  from Item, Warehouse, Batch  
        where Item.id = Batch.id_item and Batch.id_warehouse = Warehouse.id and Warehouse.id_user = $this->id_user and ".$str);
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items[] = new Item($row['id'], $row['name'], $row['price'], $row['quantity'],
                    $row['size'], $row['type'], $row['id_warehouse']);
        }
        return $Items;
    }

    public function getById($id)
    {
        return $this->getBy("Item.id = $id");
    }

    public function getByName($name)
    {
        return $this->getBy("Item.name = \"$name\";");
    }

    public function getByWarehouseId($id_warehouse)
    {
        return $this->getBy("Batch.id_warehouse = \"$id_warehouse\";");
    }

    private function getNextId()
    {
        $rows = $this->dbConnection->executeQuery("select max(Item.id) as max_id from Item
        where Item.User_id = $this->id_user;");
        $row = $rows->fetch(\PDO::FETCH_ASSOC);
        if($row['max_id'] !== null) return $row['max_id'] + 1;
        return 1;
    }
    /**
     * @param Item[]
    */
    private  function getSumSize($Items)
    {
        $sum_size = 0;
        foreach ($Items as $Item)
        {
            $sum_size += $Item->getSumSize();
        }
        return $sum_size;
    }
    /**
     * @param Item
     */
    private function newItem($Item)
    {
        if($Item->getSize() == null|| $Item->getPrice() == null || $Item->getType() == null)
        {
            return ["ERROR"=>"Товар на складах отсутствует. Укажите его характеристики"];
        }
        $current_id = $this->getNextId();
        $this->dbConnection->executeQuery('insert into Item 
            (id, name, price, size, type, User_id) 
            values (?, ?, ?, ?, ?, ?)',
            [$current_id, $Item->getName(), $Item->getPrice(), $Item->getSize(), $Item->getType(), $this->id_user]);
        $Item->setId($current_id);
        if($Item->getWarehouseId() !== null && $Item->getQuantity()!= null)
        {
            $this->dbConnection->executeQuery('insert into Batch 
            (id_item, id_warehouse, quantity) 
            values (?, ?, ?)',[$current_id, $Item->getWarehouseId(), $Item->getQuantity()]);
        }
        return $Item;
    }

    public function createItem($Item)
    {
        $current_item = $this->checkItem($Item);
        if(key_exists('ERROR',$current_item))return $this->newItem($Item);
        return ['ERROR'=>'Товар с таким именем существует'];
    }
    /**
     * @param Item
    */
    private function checkFreeCapacity($current_item)
    {
        $row = $this->dbConnection->executeQuery('select capacity
            from Warehouse where id_user = ? and id = ?;',
            [$this->id_user, $current_item->getWarehouseId()]);
        $capacity = $row->fetch(\PDO::FETCH_ASSOC);
        $items = $this->getByWarehouseId($current_item->getWarehouseId());
        $sum_size = $this->getSumSize($items);
        return ($capacity['capacity'] - $sum_size);
    }

    private function addToItem($current_item, $add_item)
    {
        $w_id = $current_item->getWarehouseId();
        $free = $this->checkFreeCapacity($current_item);
        if(($current_item->getPrice()!= $add_item->getPrice() && $add_item->getPrice()!=null)||($current_item->getSize()!= $add_item->getSize()&&$add_item->getSize()!=null )
            ||($current_item->getType()!= $add_item->getType()&&$add_item->getSize()!=null ))
            return ["ERROR"=>"Товар с таким именем уже есть на складе. Не совпадают его характеристики. Для продолжения измените имя товара или исправьте характеристики."];
        if($free >= $add_item->getSumSize())
        {
            $new_quantity = $current_item->getQuantity()+$add_item->getQuantity();
            $this->dbConnection->executeQuery('update Batch set Batch.quantity = ?
                where Batch.id_warehouse = ? and Batch.id_item = ?',
                [$new_quantity, $w_id, $current_item->getId()]);
            $row = $this->dbConnection->executeQuery('select Item.id, name, price, size, quantity, type, id_warehouse  
            from Item, Warehouse, Batch  
            where Item.id = Batch.id_item and Batch.id_warehouse = Warehouse.id and Warehouse.id_user = ? and Item.id = ?
            and Batch.id_warehouse = ?;',
                [$this->id_user, $current_item->getId(),$current_item->getWarehouseId() ]);
            $i = $row->fetch(\PDO::FETCH_ASSOC);
            return new Item($i['id'], $i['name'], $i['price'], $i['quantity'], $i['size'], $i['type'], $i['id_warehouse']);
        }else{
            return ["ERROR"=>"Мало места на складе"];
        }
    }
    private function checkItem($Item)
    {
        $row = $this->dbConnection->executeQuery('
        select * from Item where User_id = ? and name = ?;
        ', [$this->id_user, $Item->getName()]);
        $item = $row->fetch(\PDO::FETCH_ASSOC);
        if($item == null){
            return ["ERROR"=>"Товар не найден вообще. "];
        }
        $current_item = new Item(null,null,null,null,null,null,null);
        $current_item->copyFromArray($item);
        $current_item->setWarehouseId($Item->getWarehouseId());
        return $current_item;
    }

    private function checkBatch($Item)
    {
        $w_id = $Item->getWarehouseId();
        $i_id = $Item->getId();
        $rows = $this->dbConnection->executeQuery('
        select * from Batch where id_item = ? and id_warehouse = ?
        ', [$i_id, $w_id]);
        $row = $rows->fetch(\PDO::FETCH_ASSOC);
        if($row!=null) {
            return $row;
        }
        else {
            return ["ERROR"=>"Товар не найден на данном складе. "];
        }
    }

    private function sub($Item, $current_item)
    {
        if($current_item->getQuantity()<$Item->getQuantity()){
            return ['ERROR' => 'Мало продуктов.'];
        }
        else
        {
            $current_item->setQuantity($current_item->getQuantity() - $Item->getQuantity());
            return $current_item;
        }
    }

    private function subFromItem($Item, $current_item)
    {
        $current_item = $this->sub($Item, $current_item);
        if(key_exists('ERROR', $current_item)) return $current_item;
        $this->dbConnection->executeQuery('
        update Batch set quantity = ? where 
        id_item = ? and id_warehouse = ?',
            [$current_item->getQuantity(), $current_item->getId(), $current_item->getWarehouseId()]);
        return $current_item;
    }

    public function subItem($Item)
    {
        $current_item = $this->checkItem($Item);
        $current_batch_array = $this->checkBatch($Item);
        if(key_exists('ERROR',$current_item))
            return $current_item;
        if(key_exists('ERROR', $current_batch_array))
            return $current_batch_array;
        $current_item->setQuantity($current_batch_array['quantity']);
        $current_item->setWarehouseId($current_batch_array['id_warehouse']);
        return $this->subFromItem($Item, $current_item);
    }

    public function movItem(Item $subItem, $Destiny)
    {
        $sub = $this->subItem($subItem);
        if(key_exists('ERROR', $sub))
        {
            return $sub;
        }
        $Warehouse = $this->dbConnection->executeQuery('select id from Warehouse where id_user = ? and address = ?',
            [$this->id_user, $Destiny]);
        $id_warehouse_array = $Warehouse->fetch(\PDO::FETCH_ASSOC);
        if($id_warehouse_array == null)
        {
            $this->addItem($subItem);
            return ['ERROR'=>'Склад назначения не найден'];
        }
        $id_warehouse = $id_warehouse_array['id'];
        $addItem = new Item(null,null,null,null,null,null,null);
        $addItem->copyFromItem($subItem);
        $addItem->setWarehouseId($id_warehouse);
        $add = $this->addItem($addItem);
        if(key_exists('ERROR', $add))
        {
            $this->addItem($sub);
            return $add;
        }
        return $addItem;
    }

    public function addItem($Item)
    {
        $current_item = $this->checkItem($Item);
        if(key_exists('ERROR', $current_item))
            return $this->newItem($Item);
        $current_item->setWarehouseId($Item->getWarehouseId());
        $current_batch_array = $this->checkBatch($current_item);
        if(key_exists('ERROR', $current_batch_array))
        {
            $this->dbConnection->executeQuery('insert into Batch 
            (id_item, id_warehouse, quantity) 
            values (?, ?, ?)',[$current_item->getId(), $current_item->getWarehouseId(), $Item->getQuantity()]);
            $current_item->setQuantity($Item->getQuantity());
            return $current_item;
        }
        $current_item->setQuantity($current_batch_array['quantity']);
        $current_item->setWarehouseId($current_batch_array['id_warehouse']);
        return $this->addToItem($current_item, $Item);
    }

    public function deleteItem($Item)
    {
        $del = $this->checkItem($Item);
        if(key_exists('ERROR', $del)) return $del;
        $row = $this->dbConnection->executeQuery(
            'select * from Batch, Warehouse where Warehouse.id_user = ? and Batch.id_warehouse = Warehouse.id and id = ?',
            [$this->id_user, $del->getId()]);
        $batch = $row->fetch(2);
        if($batch != null)return ["ERROR"=>"Товар уже учитывается на складе"];
        $this->dbConnection->executeQuery('delete from Item where User_id = ? and id = ?',
            [$this->id_user, $del->getId()]);
        return $del;
    }

    public function updateItem($id, $new_data)
    {
        $row = $this->dbConnection->executeQuery(
            'select * from Item where User_id = ? and id = ?',
            [$this->id_user, $id]
        );
        $current_item = $row->fetch(2);
        if(!key_exists('id', $current_item)) return ["ERROR"=>"Товар не найден"];
        $row = $this->dbConnection->executeQuery(
            'select * from Batch, Warehouse where Warehouse.id_user = ? and Batch.id_warehouse = Warehouse.id and id = ?',
            [$this->id_user, $current_item['id']]);
        $batch = $row->fetch(2);
        if($new_data['name'] != null)
        {
            $rows = $this->dbConnection->executeQuery(
                'select * from Item where User_id = ? and name = ?',
                [$this->id_user, $new_data['name']]
            );
            $row = $rows->fetch(2);
            if($row['name'] == null || ($row['name'] != null && $id == $row['id']))
            {
                $this->dbConnection->executeQuery('update Item set name = ? where User_id = ? and id = ?',
                [$new_data['name'], $this->id_user, $current_item['id']]);
            }
            else return ["ERROR"=>"Имя уже существует."];
        }
        if($new_data['price'] != null)
        {
            $this->dbConnection->executeQuery('update Item set price = ? where User_id = ? and id = ?',
                [$new_data['price'], $this->id_user, $current_item['id']]);
        }
        if($new_data['type'] != null)
        {
            $this->dbConnection->executeQuery('update Item set type = ? where User_id = ? and id = ?',
                [$new_data['type'], $this->id_user, $current_item['id']]);
        }
        if($new_data['size'] != null)
        {
            if($batch != null) return ["ERROR"=>"Товар уже учитывается на складе"];
            $this->dbConnection->executeQuery('update Item set size = ? where User_id = ? and id = ?',
                [$new_data['size'], $this->id_user, $current_item['id']]);
        }
        $rows = $this->dbConnection->executeQuery(
            'select * from Item where User_id = ? and id = ?',
            [$this->id_user, $id]
        );
        $row = $rows->fetch(2);
        $new_item = new Item(null,null,null,null,null,null,null);
        $new_item ->copyFromArray($row);
        return $new_item;
    }
    //...
}