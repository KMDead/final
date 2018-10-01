<?php

namespace App\Controller;

use App\Services\WarehouseService;
use Slim\Http\Request;
use Slim\Http\Response;

class WarehouseController
{
    /**
     * @var WarehouseService
     */
    private $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function getList(Request $request, Response $response)
    {
        $warehouses = $this->warehouseService->getList();

        $jsonResponse = [];

        foreach ($warehouses as $warehouse) {
            $jsonResponse[] = [
                'id' => $warehouse->getId(),
                'address' => $warehouse->getAddress(),
                'capacity' => $warehouse->getCapacity(),
                'user_id' => $warehouse->getUserId()
            ];
        }

        return $response->withJson(
            $jsonResponse,
            200
        );
    }

    public function getById(Request $request, Response $response, $args)
    {
        $warehouse = $this->warehouseService->getById($args['id']);

        if ($warehouse === null) {
            return $response->withJson(
                [
                    'error' => 'Could not find warehouse'
                ],
                404
            );
        }

        return $response->withJson(
            [
                'id' => $warehouse->getId(),
                'address' => $warehouse->getAddress(),
                'capacity' => $warehouse->getCapacity(),
                'id_user' => $warehouse->getUserId()
            ],
            200
        );
    }

    public function getByAddress(Request $request, Response $response, $args)
    {
        $warehouse = $this->warehouseService->getByAddress($args['address']);

        if ($warehouse === null) {
            return $response->withJson(
                [
                    'error' => 'Could not find warehouse'
                ],
                404
            );
        }
        return $response->withJson(
            [
                'id' => $warehouse->getId(),
                'address' => $warehouse->getAddress(),
                'capacity' => $warehouse->getCapacity(),
                'id_user' => $warehouse->getUserId()
            ],
            200
        );
    }

    public function create(Request $request, Response $response)
    {
        $bodyParams = $request->getParsedBody();
        $address = $bodyParams['address'];
        $capacity = $bodyParams['capacity'];
        $warehouse = $this->warehouseService->create($address, $capacity);
        if(key_exists('ERROR', $warehouse))
            return $response->withJson($warehouse, 400);
        else
        return $response->withJson(
            [
                'id' => $warehouse->getId(),
                'address' => $warehouse->getAddress(),
                'capacity' => $warehouse->getCapacity(),
                'id_user'=> $warehouse->getUserId()
            ],
            200
        );
    }
    public function update(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $old_address = $bodyParams['old_address'];
        $new_address = $bodyParams['new_address'];
        $capacity = $bodyParams['capacity'];
        $warehouse = $this->warehouseService->update($old_address, $new_address, $capacity);
        if(!key_exists('ERROR', $warehouse))return $response->withJson(
        [
            'id' => $warehouse->getId(),
            'address' => $warehouse->getAddress(),
            'capacity' => $warehouse->getCapacity(),
            'id_user'=> $warehouse->getUserId()
        ],
        200);
        else return $response->withJson($warehouse, 400);

    }
    public function delete(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $address = $bodyParams['address'];
        $deleted = $this->warehouseService->delete($address);
        if(key_exists('ERROR', $deleted)) return $response->withJson($deleted, 400);
        return $response->withJson(
            [
                'id' => $deleted->getId(),
                'address' => $deleted->getAddress(),
                'capacity' => $deleted->getCapacity(),
                'id_user'=> $deleted->getUserId()
            ],
            200);
    }

}