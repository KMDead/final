<?php

namespace App\Services;

use App\Model\Warehouse;
use App\Repository\WarehouseRepository;

class WarehouseService
{
    /**
     * @var WarehouseRepository
     */
    private $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository) {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * @return Warehouse[]
     */
    public function getList()
    {
        return $this->warehouseRepository->getAll();
    }

    /**
     * @param int $id
     * @return Warehouse|null
     */
    public function getById($id)
    {
        return $this->warehouseRepository->getById($id);
    }

    /**
     * @param string
     * @return Warehouse|null
     */
    public function getByAddress($address)
    {
        return $this->warehouseRepository->getByAddress($address);
    }
    /**
     * @param string $name
     * @param string $address
     * @return Warehouse
     */
    public function create($address, $capacity)
    {
        $warehouse = new Warehouse(null, $address, $capacity, null);
        return $this->warehouseRepository->add($warehouse);
    }

    public function update($old_address, $new_address, $capacity)
    {
        return $this->warehouseRepository->updateWarehouse($old_address, $new_address, $capacity);
    }

    public function delete($address)
    {
        return $this->warehouseRepository->deleteWarehouse($address);
    }
}