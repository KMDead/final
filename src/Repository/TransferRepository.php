<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 01.10.2018
 * Time: 15:13
 */

namespace App\Repository;

use App\Model\Transfer;
use App\Model\Item;

class TransferRepository extends AbstractRepository
{
    public function __construct($dbConnection)
    {
        parent::__construct($dbConnection);
    }

    public function getTransferBy($str)
    {
        $rows = $this->dbConnection->executeQuery('
        select distinct Transfer.id, date, is_foreign, Transfer.type, Transfer.id_warehouse, Foreign_transfer.name from Transfer, Foreign_transfer, Item, Warehouse, User, Transfer_batch where 
        Transfer.id = id_transfer and Transfer.id_warehouse = Warehouse.id and id_user = User.id and Transfer.id = Foreign_transfer.id_transfer and Transfer_batch.Item_id = Item.id and 
        Transfer_batch.Transfer_id = Transfer_id 
        '.$str);
        $Transfers = [];
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items = $this->getItemsOfTransfer($row['id']);
            $transfer = new Transfer($row['id'], $row['date'], $row['is_foreign'], $row['type'],$row['id_warehouse'], $row['name'], $Items);
            array_push($Transfers, $transfer);
        }
        return $Transfers;
    }

    public function getItemsOfTransfer($transfer_id)
    {
        $rows = $this->dbConnection->executeQuery('
        select Item.id, Item.name, Item.price, Item.size, Item.type, Transfer_batch.quantity, Transfer.id_warehouse  from
        Warehouse, Transfer, Transfer_batch, Item where Item.User_id = ? and Transfer.id = ? and Warehouse.id = Transfer.id_warehouse 
        and Transfer.id = Transfer_batch.Transfer_id and Item_id = Item.id;
        ', [$this->id_user, $transfer_id]);
        $Items = [];
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items[] = new Item($row['id'], $row['name'], $row['price'],
                $row['quantity'], $row['size'], $row['type'], $row['id_warehouse']);
        }
        return $Items;
    }

    public function addNewTransfer(Transfer $transfer)
    {
        if($transfer->getId()!=null) {
            $this->dbConnection->executeQuery('
            insert into Transfer (id, date, is_foreign, type, id_warehouse) values (?,?,?,?,?);
        ', [$transfer->getId(), $transfer->getDate(), $transfer->Is_foreign(), $transfer->getType(), $transfer->getWarehouseId()]);
            $id = $this->dbConnection->lastInsertId();
        }else
        {
            $this->dbConnection->executeQuery('
            insert into Transfer (date, is_foreign, type, id_warehouse) values (?,?,?,?);
        ', [$transfer->getDate(), $transfer->Is_foreign(), $transfer->getType(), $transfer->getWarehouseId()]);
            $id = $this->dbConnection->lastInsertId();
        }
        if($transfer->Is_foreign() == 'yes') {
            $this->dbConnection->executeQuery('
            insert into Foreign_transfer values (?,?);', [$transfer->getForeignName(), $id]);
        }
        if($transfer->getItems()==null) return $id;
        foreach ($transfer->getItems() as $item) {
            $this->dbConnection->executeQuery('
            insert into Transfer_batch values (?,?,?);', [$id, $item->getId(), $item->getQuantity()]);
        }
        return $id;
    }

    /**
     * @param \DateTime, string
     * @param int
     * @param Item[]
     */
    public function warehouseComeBack($date, $id_warehouse, $current_items)
    {
        /**
         *@var Transfer[]
         */
        $transfers = $this->getTransferBy("and date>$date and id_warehouse = $id_warehouse;");
        foreach ($transfers as $transfer) {
            /**
             *@var Item[]
             */
            $Batches = $transfer->getItems();
            foreach ($Batches as $batch) {
                foreach ($current_items as $current_batch)
                    if($current_batch->getId() == $batch->getId() && $current_batch->getWarehouseId() == $batch->getWarehouseId() )
                    {
                        echo $current_batch->getQuantity()."-".$batch->getQuantity();
                        if($transfer->getType()=='add')$current_batch->setQuantity($current_batch->getQuantity()-$batch->getQuantity());
                        if($transfer->getType()=='sub')$current_batch->setQuantity($current_batch->getQuantity()+$batch->getQuantity());
                    }
            }
        }
        return $current_items;
    }

    public function batchesComeBack($date, $id_item, $current_items)
    {
        /**
         *@var Transfer[]
         */
        $transfers = $this->getTransferBy("and date>$date and Item.id = $id_item;");
        foreach ($transfers as $transfer) {
            /**
             *@var Item[]
             */
            $Batches = $transfer->getItems();
            foreach ($Batches as $batch) {
                foreach ($current_items as $current_batch)
                    if($current_batch->getWarehouseId() === $batch->getWarehouseId() && $current_batch->getId() == $batch->getId() )
                    {
                        echo $current_batch->getQuantity().' - '.$batch->getQuantity()."\n";
                        if($transfer->getType()=='add')$current_batch->setQuantity($current_batch->getQuantity()-$batch->getQuantity());
                        if($transfer->getType()=='sub')$current_batch->setQuantity($current_batch->getQuantity()+$batch->getQuantity());
                    }
            }
        }
        return $current_items;
    }
}