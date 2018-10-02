<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 17.09.2018
 * Time: 19:15
 */

namespace App\Services;

use App\Model\Item;
use App\Repository\ItemRepository;

class ItemService
{
    /**
     * @var ItemRepository
    */
    private $ItemRepository;
    public function __construct($ItemRepository)
    {
        $this->ItemRepository = $ItemRepository;
    }
    /**
     * @return Item[]
     */
    public function getList()
    {
        return $this->ItemRepository->getAllFromItem();
    }
    public function getBy($str)
    {
        return $this->ItemRepository->getBy($str);
    }
    public function getById($id)
    {
        return $this->ItemRepository->getById($id);
    }
    public function getItemById($id)
    {
        return $this->ItemRepository->getItemById($id)[0];
    }
    public function getByName($name)
    {
        return $this->ItemRepository->getByName($name);
    }
    public function getByWarehouseId($id_warehouse)
    {
        return $this->ItemRepository->getByWarehouseId($id_warehouse);
    }

    public function addItem($name, $price, $quantity, $size, $type, $id_warehouse)
    {
        $addItem = new Item(null, $name, $price,
            $quantity, $size, $type, $id_warehouse);
        return $this->ItemRepository->addItem($addItem);
    }

    public function getSumSize($Item)
    {
        return $this->ItemRepository->getSumSize($Item);
    }

    public function getSumPrice($Item)
    {
        return $this->ItemRepository->getSumPrice($Item);
    }

    public function subItem($name, $id_warehouse, $quantity)
    {
        $subItemArray = $this->getBy("name = \"$name\" and id_warehouse = $id_warehouse");
        $subItem = $subItemArray[0];
        if($subItem == null) return ['ERROR'=>'Продукт не найден'];
        $subItem->setQuantity($quantity);
        return $this->ItemRepository->subItem($subItem);
    }

    public function movItem($address_source, $address_destiny, $name_item, $quantity)
    {
        $ItemArray = $this->getBy("Item.name = \"$name_item\" and Warehouse.address = \"$address_source\";");
        if($ItemArray != null) {
            $Item = new Item(null, null, null, null, null, null, null);
            $Item->copyFromItem($ItemArray[0]);
            $Item->setQuantity($quantity);
            return $this->ItemRepository->movItem($Item, $address_destiny);
        }else{
        return ["ERROR" => "Не найден товар или склад-источник"];
        }
    }

    public function createItem($name, $price, $size, $type)
    {
        $addItem = new Item(null, $name, $price,
            null, $size, $type, null);
        return $this->ItemRepository->createItem($addItem);
    }

    public function updateItem($id, $new_data)
    {
        return $this->ItemRepository->updateItem($id, $new_data);
    }

    public function removeItem($name, $price, $size, $type)
    {
        $remItem = new Item(null, $name, $price,
            null, $size, $type, null);
        return $this->ItemRepository->deleteItem($remItem);
    }
}