<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 13.09.2018
 * Time: 20:21
 */

namespace App\Model;


class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $organisation;
    public function __construct($id, $name, $email, $password, $phone, $organisation)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->organisation = $organisation;
    }
    /**
     * @param User
    */
    public function copyFromUser($User)
    {
        $this->id = $User->getId();
        $this->name = $User->getName();
        $this->email = $User->getEmail();
        $this->password = $User->getPassword();
        $this->phone = $User->getPhone();
        $this->organisation = $User->getOrganisation();
    }
    public  function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    public function getOrganisation()
    {
        return $this->organisation;
    }
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }
}