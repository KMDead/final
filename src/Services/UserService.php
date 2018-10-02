<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 18.09.2018
 * Time: 23:16
 */

namespace App\Services;
use App\Repository\UserRepository;
use App\Model\User;
use phpDocumentor\Reflection\Types\Array_;

class UserService
{
    /**
     * @var UserRepository
     */
    private $UserRepository;
    public function __construct($UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }
    public function NewUser($name, $org, $email, $phone, $password)
    {
        return $this->UserRepository->addUser(new User(null, $name, $email, $password, $phone, $org));
    }
    public function authUser($email, $password)
    {
        return $this->UserRepository->authUser($email, $password);
    }
    public function Delete()
    {
        return $this->UserRepository->deleteUser();
    }
    public function Update($newData)
    {
        return $this->UserRepository->updateUser($newData);
    }
    public function exitUser()
    {
        return $this->UserRepository->exitUser();
    }
}