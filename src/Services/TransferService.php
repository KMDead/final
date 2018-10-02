<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 01.10.2018
 * Time: 19:10
 */


namespace App\Services;
use App\Model\Transfer;


class TransferService
{
    /**
     * @var ItemRepository
     */
    private $TransferRepository;

    public function __construct($TransferRepository)
    {
        $this->TransferRepository = $TransferRepository;
    }

    public function addTransfer($id, $date, $is_foreign, $type, $warehouse_id, $name, $items)
    {
        $new_transfer = new Transfer($id, $date, $is_foreign, $type, $warehouse_id, $name, $items);
        return $this->TransferRepository->addNewTransfer($new_transfer);
    }

    public function getTransfers()
    {
        return $this->TransferRepository->getTransferBy(";");
    }

    public function getTransfersByItem($item_name)
    {
        return $this->TransferRepository->getTransferBy(" and Item.name = \"$item_name\";");
    }

    public function getTransfersByWarehouse($address)
    {
        return $this->TransferRepository->getTransferBy(" and Warehouse.address = \"$address\";");
    }

    public function warehouseComeBack($date, $id_warehouse, $current_items)
    {
        return $this->TransferRepository->warehouseComeBack($date, $id_warehouse, $current_items);
    }

    public function batchesComeBack($date, $id_item, $current_items)
    {
        return $this->TransferRepository->batchesComeBack($date, $id_item, $current_items);
    }
}