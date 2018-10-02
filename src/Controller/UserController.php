<?php
/**
 * Created by PhpStorm.
 * User: KM
 * Date: 18.09.2018
 * Time: 23:31
 */

namespace App\Controller;
use App\Services\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController
{
    /**
     * @var UserService
     */
    private $userService;
    public function __construct($userService)
    {
        $this->userService = $userService;
    }
    public function NewUser(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $name = $bodyParams['name'];
        $organisation = $bodyParams['organisation'];
        $email = $bodyParams['email'];
        $phone = $bodyParams['phone'];
        $password = $bodyParams['password'];
        if($password == null ||$name == null || $organisation == null || $email == null || $phone == null)
            $response->withJson(["ERROR" => "Заполните все поля."], 400);
        $newUser = $this->userService->NewUser($name, $organisation, $email, $phone, $password);
        if(!key_exists('ERROR', $newUser))
        return $response->withJson(
            [
                'id' => $newUser->getId(),
                'name' => $newUser->getName(),
                'organisation' => $newUser->getOrganisation(),
                'email' => $newUser->getEmail(),
                'phone' => $newUser->getPhone(),
                'password' => $newUser->getPassword()
            ],
            200
        );
        else return $response->withJson($newUser, 400);
    }
    public function AuthUser(Request $request, Response $response, $args)
    {
        $bodyParams = $request->getParsedBody();
        $email = $bodyParams['email'];
        $password = $bodyParams['password'];
        $result = $this->userService->authUser($email, $password);
        $str = $result['str'];
        if(!key_exists('ERROR', $result))
        {
            setcookie("email", $email,  0,'/');
            setcookie("str", $str,  0,'/');
            return $response->withJson(["Статус"=>"Успешно!"], 200);
        }
        else return $response->withJson($result, 400);
    }
    public function UpdateUser(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $newUser = $this->userService->Update($data);
        if(!key_exists('ERROR', $newUser)) {
            return $response->withJson(
                [
                    'id' => $newUser->getId(),
                    'name' => $newUser->getName(),
                    'organisation' => $newUser->getOrganisation(),
                    'email' => $newUser->getEmail(),
                    'phone' => $newUser->getPhone(),
                    'password' => $newUser->getPassword()
                ],
                200
            );
        }
        else return $response->withJson($newUser, 400);
    }
    public function DeleteUser(Request $request, Response $response, $args)
    {

        $newUser = $this->userService->Delete();
        if(!key_exists('ERROR',$newUser))
            return $response->withJson(
                [
                    'id' => $newUser->getId(),
                    'name' => $newUser->getName(),
                    'organisation' => $newUser->getOrganisation(),
                    'email' => $newUser->getEmail(),
                    'phone' => $newUser->getPhone(),
                    'password' => $newUser->getPassword()
                ],
                200
            );
        else return $response->withJson($newUser, 400);
    }
    public function ExitUser(Request $request, Response $response, $args)
    {
        $newUser = $this->userService->exitUser();
        if(!key_exists('ERROR',$newUser))
            return $response->withJson(
                [
                    'id' => $newUser->getId(),
                    'name' => $newUser->getName(),
                    'organisation' => $newUser->getOrganisation(),
                    'email' => $newUser->getEmail(),
                    'phone' => $newUser->getPhone(),
                    'password' => $newUser->getPassword()
                ],
                200
            );
        else return $response->withJson($newUser, 400);
    }
}