<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 18.09.2018
 * Time: 20:00
 */

namespace App\Repository;


use App\Model\User;

class UserRepository extends AbstractRepository
{
    /**
     * @var User
    */
    private function checkUniq($User)
    {
        $rows = $this->dbConnection->executeQuery(
            'select * from final_work.User where (name = ? and organisation = ?) or (email = ?)',
            [$User->getName(), $User->getOrganisation(), $User->getEmail()]
        );
        $data = $rows->fetch(\PDO::FETCH_ASSOC);
        return $data;
    }

    private function getUser()
    {
        $rows = $this->dbConnection->executeQuery(
            'select * from final_work.User where User.id = ?',
            [$this->id_user]
        );
        $data = $rows->fetch(\PDO::FETCH_ASSOC);
        if($data == null) return ['ERROR'=>'Пользователь не найден'];
        return $data;
    }

    public function deleteUser()
    {
        $oldData = $this->getUser($this->id_user);
        if(key_exists('ERROR', $oldData))return $oldData;
        $deletedUser = new User($oldData['id'], $oldData['name'], $oldData['email'],$oldData['password'], $oldData['phone'], $oldData['organisation']);
        $this->dbConnection->executeQuery(
            'delete from Foreign_transfer where id_transfer in  
              (select id from Transfer where id_warehouse in (select id from Warehouse where id_user = ?));
            delete from Transfer_batch where Transfer_id in  
              (select id from Transfer where id_warehouse in (select id from Warehouse where id_user = ?));
            delete from Transfer where id_warehouse in (select id from Warehouse where id_user = ?);
            delete from Batch where id_warehouse in (select id from Warehouse where id_user = ?);
            delete from Item where User_id = ?;
            delete from Warehouse where id_user = ?;
            delete from User where id = ?;',
            [$this->id_user, $this->id_user, $this->id_user, $this->id_user, $this->id_user,$this->id_user,$this->id_user]
        );
        return $deletedUser;
    }
    public function exitUser()
    {
        $data = $this->getUser();
        $User = new User($data['id'], $data['name'], $data['email'], $data['password'], $data['phone'], $data['organisation']);
        if(key_exists('ERROR', $data)) return $data;
        $this->dbConnection->executeQuery(
            'update User set str = null where User.id = ?',
            [$this->id_user]
        );
        setcookie('email', null, 0, '/');
        setcookie('str', null, 0, '/');
        return $User;

    }
    public function addUser($User)
    {
        $u = $this->checkUniq($User);
        if($u['name'] == $User->getName()) return ["ERROR"=>"Имя уже используется"];
        if($u['email'] == $User->getEmail()) return ["ERROR"=>"Эл.почта уже используется"];
        $this->dbConnection->executeQuery(
            'INSERT INTO final_work.User (id, name, organisation, email, phone, password) values (?, ?, ?, ?, ?, ?)',
            [$User->getId(), $User->getName(), $User->getOrganisation(), $User->getEmail(), $User->getPhone(), $User->getPassword()]
        );
        $id = $this->dbConnection->lastInsertId();
        $User->setId($id);
        return $User;
    }
    public function authUser($email, $password)
    {
        $row = $this->dbConnection->executeQuery('
        select * from User where email = ? and password = ?;
        ',[$email, $password]);
        $result = $row->fetch(2);
        if($result['id']!=null)
        {
            $this->dbConnection->executeQuery('
            update User set str = (SELECT SUBSTRING( MD5(RAND()) FROM 1 FOR 8)) where email = ? and password = ?;
            ',[$email, $password]);
            $row = $this->dbConnection->executeQuery('
            select * from User where email = ? and password = ?;
            ',[$email, $password]);
            $result = $row->fetch(2);
            return $result;
        }
        else return ["ERROR"=>"Ошибка аутентификации"];
    }
    public function updateUser($newUserDataArray)
    {
        $u = $this->getUser();
        $newUser = new User($u['id'], $u['name'], $u['email'], $u['password'], $u['phone'], $u['organisation']);
        if(key_exists('name', $newUserDataArray)){$newUser->setName($newUserDataArray['name']);}
        if(key_exists('organisation', $newUserDataArray)){ $newUser->setOrganisation($newUserDataArray['organisation']);}
        if(key_exists('email', $newUserDataArray)){$newUser->setEmail($newUserDataArray['email']);}
        if(key_exists('password', $newUserDataArray)){$newUser->setPassword($newUserDataArray['password']);}
        if(key_exists('phone', $newUserDataArray)){$newUser->setPhone($newUserDataArray['phone']);}
        $id = $this->id_user;
        if($this->checkUniq($newUser)['id'] == $id)
        {
            $this->dbConnection->executeQuery('
            update User set name = ?, organisation = ?, email = ?, password = ?, phone = ? where id = ?
            ', [$newUser->getName(), $newUser->getOrganisation(), $newUser->getEmail(), $newUser->getPassword(), $newUser->getPhone(), $id]);
            setcookie('email', $newUser->getEmail(), 0, '/');
        }else
        {
            return ["ERROR"=>"Неверные данн11111ые"];
        }
        return $newUser;
    }
}