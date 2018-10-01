<?php

namespace App\Tests\Functional;

class WarehouseTest extends ApiTestCase
{
    public function setUp()
    {

        parent::setUp();
        $container = $this->app->getContainer();
        $dbConnection = $container->get('db');

        $dbConnection->executeQuery(
            'DELETE FROM warehouse.warehouse'
        );

        $dbConnection->executeQuery(
            'INSERT INTO warehouse
  (id, name, address)
VALUES
  (1, \'name1\', \'address1\'),
  (2, \'name2\', \'address2\'),
  (3, \'name3\', \'address3\');'
        );
    }

    public function testGetList()
    {
        $this->request('GET', '/api/v1/warehouses');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());

        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'name1', 'address' => 'address1'],
                ['id' => '2', 'name' => 'name2', 'address' => 'address2'],
                ['id' => '3', 'name' => 'name3', 'address' => 'address3'],
            ],
            $this->responseData()
        );
    }

    public function testGetOne()
    {
        $this->request('GET', '/api/v1/warehouses/1');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');

        $this->assertEquals(
            ['id' => '1', 'name' => 'name1', 'address' => 'address1'],
            $this->responseData()
        );
    }

    public function testAdd()
    {
        $this->request('POST', 'api/v1/warehouses', ['name'=>'name7', 'address'=>'address7']);

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');

        $responteData = $this->responseData();

        $this->assertEquals('name7', $responteData['name']);
        $this->assertEquals('address7', $responteData['address']);
    }
}