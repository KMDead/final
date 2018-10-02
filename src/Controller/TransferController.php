<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 01.10.2018
 * Time: 19:31
 */

namespace App\Controller;
use App\Model\Transfer;
use App\Model\Item;
use App\Model\Warehouse;
use App\Services\TransferService;
use App\Services\ItemService;
use App\Services\WarehouseService;
use Slim\Http\Request;
use Slim\Http\Response;

class TransferController
{
    /**
     * @var TransferService
     */
    private $ItemService;
    private $TransferService;
    /**
     * @var WarehouseService
     */
    private $WarehouseService;
    public function __construct($TransferService, $ItemService, $WarehouseService)
    {
        $this->TransferService = $TransferService;
        $this->ItemService = $ItemService;
        $this->WarehouseService = $WarehouseService;
    }
    private function madeArray($bodyParams)
    {
        $jsonResponse = [];
        if($bodyParams['items']==null|| $bodyParams['quantity'] == null)return ['ERROR'=>'укажите товар и его количество'];
        $item_id_array = explode(',', $bodyParams['items']);
        $quantity_array = explode(',', $bodyParams['quantity']);
        $item_array = array_combine($item_id_array, $quantity_array);
        $items = [];
        foreach($item_array as $id => $quantity)
        {
            $item = $this->ItemService->getItemById($id);
            if($item != null)
            {
                $item->setQuantity($quantity);
                array_push($items, $item);
            }
            else {
                $jsonResponse[] = ['id' => $id, 'status' => 'не найдено'];
            }
        }
        $res = Array('items' => $items, 'ERRORS'=>$jsonResponse);
        return $res;
    }
    public function addForeignItem(Request $request, Response $response, $args)
    {
        $Items = [];
        $bodyParams = $request->getParsedBody();
        $source_name = $bodyParams['source_name'];
        $warehouse_address = $bodyParams['address'];
        if($warehouse_address==null) return $response->withJson('Укажите адрес', 400);
        if($source_name==null) return $response->withJson('Укажите источник товара', 400);
        $w = $this->WarehouseService->getByAddress($warehouse_address);
        if($w->getId() == null) return $response->withJson('Склад не найден', 400);
        $array = $this->madeArray($bodyParams);
        if(key_exists('ERROR', $array))return $response->withJson($array, 400);
        $items = $array['items'];
        $jsonResponse = $array['ERRORS'];
        foreach ($items as $item)
        {
            $Item = $this->ItemService->addItem($item->getName(), $item->getPrice(),
                $item->getQuantity(), $item->getSize(), $item->getType(), $w->getId());
            if(!key_exists('ERROR',$Item)) {
                $res = [
                    'id'=>$item->getId(),
                    'name' => $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'status' => 'Успешно'
                ];
                array_push($jsonResponse, $res);
                array_push($Items, $item);
            }else
            {
                $res = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'status' => $Item
                ];
                array_push($jsonResponse, $res);
            }
        }
        if($Items == []) return $response->withJson($jsonResponse, 400);
        $this->TransferService->addTransfer(null, date('Y-m-d H:i:s'), 'yes', 'add', $w->getId(), $source_name, $Items);
        return $response->withJson($jsonResponse, 200);
    }
    public function subForeignItem(Request $request, Response $response, $args)
    {
        $Items = [];
        $bodyParams = $request->getParsedBody();
        $destiny_name = $bodyParams['destiny_name'];
        $warehouse_address = $bodyParams['address'];
        if($warehouse_address==null) return $response->withJson('Укажите адрес', 400);
        if($destiny_name==null) return $response->withJson('Укажите получателя товара', 400);
        $w = $this->WarehouseService->getByAddress($warehouse_address);
        if( $w->getId() == null) return $response->withJson("Склад не найден", 400);
        $array = $this->madeArray($bodyParams);
        if(key_exists('ERROR', $array))return $response->withJson($array, 400);
        $items = $array['items'];
        $jsonResponse = $array['ERRORS'];
        foreach ($items as $item)
        {
            $Item = $this->ItemService->subItem($item->getName(), $w->getId(), $item->getQuantity());
            if(!key_exists('ERROR',$Item)) {
                $res = [
                    'id'=>$item->getId(),
                    'name' => $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'status' => 'Успешно'
                ];
                array_push($jsonResponse, $res);
                array_push($Items, $item);
            }else
            {
                $res = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'status' => $Item
                ];
                array_push($jsonResponse, $res);
            }
        }
        if($Items == []) return $response->withJson('Продукты не найдены', 400);
        $this->TransferService->addTransfer(null, date('Y-m-d H:i:s'), 'yes', 'sub', $w->getId(), $destiny_name, $Items);
        return $response->withJson($jsonResponse, 200);
    }
    public function movItem(Request $request, Response $response, $args)
    {
        $Items = [];
        $bodyParams = $request->getParsedBody();
        $destiny = $bodyParams['address_destiny'];
        $source = $bodyParams['address_source'];
        $source_id = $this->WarehouseService->getByAddress($source)->getId();
        $destiny_id = $this->WarehouseService->getByAddress($destiny)->getId();
        $array = $this->madeArray($bodyParams);
        if(key_exists('ERROR', $array))return $response->withJson($array, 400);
        $items = $array['items'];
        $jsonResponse = $array['ERRORS'];
        foreach ($items as $item)
        {
            $Item = $this->ItemService->movItem($source, $destiny, $item->getName(), $item->getQuantity());
            if(!key_exists('ERROR',$Item)) {
                $res = [
                    'id'=>$item->getId(),
                    'name' => $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'status' => 'Успешно'
                ];
                array_push($jsonResponse, $res);
                array_push($Items, $item);
            }else
            {
                $res = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'status' => $Item
                ];
                array_push($jsonResponse, $res);
            }
        }
        if($Items == []) return $response->withJson('Продукты не найдены', 400);
        $id = $this->TransferService->addTransfer(null, date('Y-m-d H:i:s'), 'no', 'sub', $source_id, null, $Items);
        $this->TransferService->addTransfer($id, date('Y-m-d H:i:s'), 'no', 'add', $destiny_id, null, null);
        return $response->withJson($jsonResponse, 200);
    }
    public function getAll(Request $request, Response $response, $args)
    {
        $transfers = $this->TransferService->getTransfers();
        if($transfers == [])return $response->withJson("Пусто", 400);
        $jsonResponse = [];
        foreach ($transfers as $t)
        {
            $Items = [];
            $items = $t->getItems();
            foreach ($items as $item)
            {
                $Items[]=[
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                    'size' => $item->getSize(),
                    'type' => $item->getType(),
                    'warehouse_id' => $item->getWarehouseId(),
                ];
            }
            $jsonResponse[]=[
                "id"=>$t->getId(),
                "date"=>$t->getDate(),
                "is_foreign"=>$t->Is_foreign(),
                "type"=>$t->getType(),
                "name"=>$t->getForeignName(),
                "warehouse_id"=>$t->getWarehouseId(),
                "items"=>$Items
            ];
        }
        return $response->withJson($jsonResponse, 200);
    }
    public function getByItem(Request $request, Response $response, $args)
    {
        $transfers = $this->TransferService->getTransfersByItem($args['name']);
        if($transfers == [])return $response->withJson("Пусто", 400);
        $jsonResponse = [];
        foreach ($transfers as $t)
        {
            $Items = [];
            $items = $t->getItems();
            foreach ($items as $item)
            {
                $Items[]=[
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                    'size' => $item->getSize(),
                    'type' => $item->getType(),
                    'warehouse_id' => $item->getWarehouseId(),
                ];
            }
            $jsonResponse[]=[
                "id"=>$t->getId(),
                "date"=>$t->getDate(),
                "is_foreign"=>$t->Is_foreign(),
                "type"=>$t->getType(),
                "name"=>$t->getForeignName(),
                "warehouse_id"=>$t->getWarehouseId(),
                "items"=>$Items
            ];
        }
        return $response->withJson($jsonResponse, 200);
    }
    public function getByWarehouse(Request $request, Response $response, $args)
    {
        $transfers = $this->TransferService->getTransfersByWarehouse($args['address']);
        if($transfers == [])return $response->withJson("Пусто", 400);
        $jsonResponse = [];
        foreach ($transfers as $t)
        {
            $Items = [];
            $items = $t->getItems();
            foreach ($items as $item)
            {
                $Items[]=[
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                    'size' => $item->getSize(),
                    'type' => $item->getType(),
                    'warehouse_id' => $item->getWarehouseId(),
                ];
            }
            $jsonResponse[]=[
                "id"=>$t->getId(),
                "date"=>$t->getDate(),
                "is_foreign"=>$t->Is_foreign(),
                "type"=>$t->getType(),
                "name"=>$t->getForeignName(),
                "warehouse_id"=>$t->getWarehouseId(),
                "items"=>$Items
            ];
        }
        return $response->withJson($jsonResponse, 200);
    }
}