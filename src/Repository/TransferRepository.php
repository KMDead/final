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
        select id, date, is_foreign, type, id_warehouse, name from Transfer, Foreign_transfer where 
        Transfer.id = id_transfer and '.$str);
        $Transfers = [];
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items = $this->getItemsOfTransfer($row['id']);
            $Transfers[] = new Transfer($row['id'], $row['date'], $row['is_foreign'], $row['type'],$row['id_warehouse'], $row['name'], $Items);
        }
        return $Transfers;
    }

    public function getItemsOfTransfer($transfer_id)
    {
        $rows = $this->dbConnection->executeQuery('
        select Item.id, name, price, size, Item.type, Transfer_batch.quantity, id_warehouse from Transfer_batch, Item, Batch where 
        Transfer_id = ? and User.id = ? and Item.id = id_item and Item.id = Item_id;
        ', [$transfer_id, $this->id_user]);
        $Items = [];
        while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
            $Items[] = new Item($row['id'], $row['name'], $row['price'],
                $row['quantity'], $row['size'], $row['type'], $row['id_warehouse']);
        }
        return $Items;
    }

    public function addNewTransfer(Transfer $transfer)
    {
        $this->dbConnection->executeQuery('
            insert into Transfer (id, date, is_foreign, type, id_warehouse) values (?,?,?,?,?);
        ', [$transfer->getId(), $transfer->getDate(), $transfer->Is_foreign(), $transfer->getType(), $transfer->getWarehouseId()]);
        $id = $transfer->getId();
        if($transfer->Is_foreign() == 1)
        {
            $this->dbConnection->executeQuery('
            insert into Foreign_transfer (id_transfer, name) values (?,?);
        ', [$id, $transfer->getForeignName()]);
        }
        $Items = $transfer->getItems();
        foreach($Items as $Item) {
            $this->dbConnection->executeQuery('
            insert into Transfer_batch (Transfer_id, Item_id, quantity) values (?,?,?);
        ', [$id, $Item->getId(), $Item->getQuantity()]);
        }
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
        $transfers = $this->getTransferBy("date>$date and id_warehouse = $id_warehouse;");
        foreach ($transfers as $transfer) {
            /**
             *@var Item[]
             */
            $Batches = $transfer->getItems();
            foreach ($Batches as $batch) {
                foreach ($current_items as $current_batch)
                if($current_batch->getId() == $batch->getId())
                    $current_items->setQuantity($current_batch->getQuantity()-$batch->getQuantity());
            }
        }
        return $current_items;
    }

    public function batchesComeBack($date, $id_item, $current_items)
    {
        /**
         *@var Transfer[]
         */
        $transfers = $this->getTransferBy("date>$date and Item_id = $id_item;");
        foreach ($transfers as $transfer) {
            /**
             *@var Item[]
             */
            $Batches = $transfer->getItems();
            foreach ($Batches as $batch) {
                foreach ($current_items as $current_batch)
                    if($current_batch->getId() == $batch->getId())
                        $current_items->setQuantity($current_batch->getQuantity()-$batch->getQuantity());
            }
        }
        return $current_items;
    }
}