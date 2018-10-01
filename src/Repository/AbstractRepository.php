<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

use PHPUnit\Runner\Exception;

abstract class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $dbConnection;
    protected $id_user;
    public function __construct(Connection $dbConnection)
    {
        $str = null;
        $email = null;
        $this->dbConnection = $dbConnection;
        try{
            if(isset($_COOKIE["str"]) && isset($_COOKIE["email"])) {
                $str = $_COOKIE["str"];
                $email = $_COOKIE["email"];
            }
            else throw new Exception("Пройдите регистрацию и/или аутентификацию. ", 400);
            $row = $this->dbConnection->executeQuery(
                   'select id from User where str = ? and email = ?',
                   [$str, $email]
            );
            $id_array = $row->fetch(2);
            $this->id_user = $id_array['id'];
        }
        catch(Exception $exception)
        {
            echo $exception->getMessage();
        }
    }
}