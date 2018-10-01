<?php

use Doctrine\DBAL\DriverManager;
use App\Controller\WarehouseController;
use App\Controller\ItemController;
use App\Controller\UserController;
use App\Repository\WarehouseRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Services\ItemService;
use App\Services\UserService;
use App\Services\WarehouseService;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container['db'] = function () {
    return DriverManager::getConnection([
        'driver' => 'pdo_mysql',
        //'host' => '192.168.100.123',
        'host' => '127.0.0.1',
        'dbname' => 'final_work',
        'user' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ]);
};


// init resources

$container['warehouse.controller'] = function ($c) {
    /** @var ContainerInterface $c */
    return new WarehouseController($c->get('warehouse.service'));
};

$container['item.controller'] = function ($c) {
    /** @var ContainerInterface $c */
    return new ItemController($c->get('item.service'));
};

$container['user.controller'] = function ($c) {
    /** @var ContainerInterface $c */
    return new UserController($c->get('user.service'));
};

$container['warehouse.service'] = function ($c) {
    /** @var ContainerInterface $c */
    return new WarehouseService($c->get('warehouse.repository'));
};

$container['item.service'] = function ($c)
{
    /** @var ContainerInterface $c */
    return new ItemService($c->get('item.repository'));
};

$container['user.service'] = function ($c)
{
    /** @var ContainerInterface $c */
    return new UserService($c->get('user.repository'));
};


$container['warehouse.repository'] = function ($c) {
    /** @var ContainerInterface $c */
    return new WarehouseRepository($c->get('db'));
};

$container['item.repository'] = function ($c) {
    /** @var ContainerInterface $c */
    return new ItemRepository($c->get('db'));
};

$container['user.repository'] = function ($c) {
    /** @var ContainerInterface $c */
    return new UserRepository($c->get('db'));
};
// init routes
$app->group('/api', function () use ($app) {
    $app->get('/warehouses', 'warehouse.controller:getList');
    $app->get('/warehouses/id/{id}', 'warehouse.controller:getById');
    $app->get('/warehouses/address/{address}', 'warehouse.controller:getByAddress');
    $app->post('/warehouses/add', 'warehouse.controller:create');
    $app->post('/warehouses/update', 'warehouse.controller:update');
    $app->post('/warehouses/delete', 'warehouse.controller:delete');
    $app->get('/items', 'item.controller:getList');
    $app->get('/items/id/{id}', 'item.controller:getById');
    $app->get('/items/name/{name}', 'item.controller:getByName');
    $app->get('/items/warehouse/{warehouse_id}', 'item.controller:getByWarehouseId');
    $app->post('/items/create', 'item.controller:createItem');
    $app->post('/items/remove', 'item.controller:removeItem');
    $app->post('/items/update', 'item.controller:updateItem');
    $app->post('/items/add', 'item.controller:addItem');
    $app->post('/items/sub', 'item.controller:subItem');
    $app->post('/items/mov', 'item.controller:movItem');
    $app->post('/user/registration', 'user.controller:newUser');
    $app->post('/user/authentication', 'user.controller:AuthUser');
    $app->get('/user/delete', 'user.controller:DeleteUser');
    $app->post('/user/update', 'user.controller:UpdateUser');
});
