<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 17.09.2018
 * Time: 19:07
 */

namespace App\Controller;

use App\Model\Item;
use App\Services\ItemService;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

class ItemController
{
    /**
     * @var ItemService
    */
    private $ItemService;
    public function __construct($ItemService)
    {
        $this->ItemService = $ItemService;
    }

    public function getList(Request $request, Response $response)
    {
        $Items = $this->ItemService->getList();
        $jsonResponse = [];
        foreach ($Items as $Item) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'warehouse_id' => $Item->getWarehouseId()
            ];
        }
        return $response->withJson(
            $jsonResponse,
            200
        );
    }

    public function getById(Request $request, Response $response, $args)
    {
        $Items = $this->ItemService->getById($args['id']);
        $jsonResponse = [];
        foreach ($Items as $Item) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'warehouse_id' => $Item->getWarehouseId()
            ];
        }
        return $response->withJson(
            $jsonResponse,
            200
        );
    }

    public function getByName(Request $request, Response $response, $args)
    {
        $Items = $this->ItemService->getByName($args['name']);
        $jsonResponse = [];
        foreach ($Items as $Item) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'warehouse_id' => $Item->getWarehouseId()
            ];
        }
        return $response->withJson(
            $jsonResponse,
            200
        );
    }

    public function getByWarehouseId(Request $request, Response $response, $args)
    {
        $Items = $this->ItemService->getByWarehouseId($args['warehouse_id']);
        $jsonResponse = [];
        foreach ($Items as $Item) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'warehouse_id' => $Item->getWarehouseId()
            ];
        }
        return $response->withJson(
            $jsonResponse,
            200
        );
    }

    public function addItem(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $Item = $this->ItemService->addItem($bodyParams['name'], $bodyParams['price'],
            $bodyParams['quantity'], $bodyParams['size'], $bodyParams['type'], $bodyParams['id_warehouse']);
        if(!key_exists('ERROR',$Item)) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'id_warehouse' => $Item->getWarehouseId()
            ];
            return
                $response->withJson(
                    $jsonResponse,
                    200
                );
        }else return $response->withJson($Item, 400);
    }

    public function createItem(Request $request, Response $response, $args)
    {
        echo "str = ".$_COOKIE['str'];
        $bodyParams = $request->getParsedBody();
        $Item = $this->ItemService->createItem($bodyParams['name'], $bodyParams['price'], $bodyParams['size'], $bodyParams['type']);
        if(!key_exists('ERROR',$Item)) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'id_warehouse' => $Item->getWarehouseId()
            ];
            return
                $response->withJson(
                    $jsonResponse,
                    200
                );
        }else return $response->withJson($Item, 400);
    }

    public function subItem(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $name = $bodyParams['name'];
        $id_warehouse = $bodyParams['id_warehouse'];
        $quantity = $bodyParams['quantity'];
        $Item = $this->ItemService->subItem($name, $id_warehouse, $quantity);
        if(!key_exists('ERROR',$Item)) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'id_warehouse' => $Item->getWarehouseId()
            ];
            return
                $response->withJson(
                    $jsonResponse,
                    200
                );
        }else return $response->withJson($Item, 400);
    }

    public function movItem(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $address_source = $bodyParams['address_source'];
        $address_destiny = $bodyParams['address_destiny'];
        $name_item = $bodyParams['name_item'];
        $quantity = $bodyParams['quantity'];
        $movResult = $this->ItemService->movItem($address_source, $address_destiny, $name_item, $quantity);
        if(key_exists("ERROR", $movResult))
            return $response->withJson($movResult, 400);
        else
        {
             $jsonResponse[] = [
             'id' => $movResult->getId(),
             'name' => $movResult->getName(),
             'price' => $movResult->getPrice(),
             'size' => $movResult->getSize(),
             'quantity' => $movResult->getQuantity(),
             'type' => $movResult->getType(),
             'id_warehouse' => $movResult->getWarehouseId()
              ];
        return $response->withJson($jsonResponse, 200);
        }

    }
    public function updateItem(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $Item = $this->ItemService->updateItem($bodyParams['id'], $bodyParams);
        if(!key_exists('ERROR',$Item)) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'id_warehouse' => $Item->getWarehouseId()
            ];
            return
                $response->withJson(
                    $jsonResponse,
                    200
                );
        }else return $response->withJson($Item, 400);
    }
    public function removeItem(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $Item = $this->ItemService->removeItem($bodyParams['name'], $bodyParams['price'], $bodyParams['size'], $bodyParams['type']);
        if(!key_exists('ERROR',$Item)) {
            $jsonResponse[] = [
                'id' => $Item->getId(),
                'name' => $Item->getName(),
                'price' => $Item->getPrice(),
                'size' => $Item->getSize(),
                'quantity' => $Item->getQuantity(),
                'type' => $Item->getType(),
                'id_warehouse' => $Item->getWarehouseId()
            ];
            return
                $response->withJson(
                    $jsonResponse,
                    200
                );
        }else return $response->withJson($Item, 400);
    }
}