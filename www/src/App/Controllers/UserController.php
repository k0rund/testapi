<?php
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Models\UserModel;

class UserController extends PublicController
{

    /**
     * Cоздание нового пользователя
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $args
     */
    public function create(RequestInterface $request, ResponseInterface $response, $args)
    {
        $allPostPutVars = $request->getParsedBody();
        $userModel = new UserModel();
        $result = $userModel->createUser($allPostPutVars['login'] ,$allPostPutVars['pass']);
        if ($result) {
            $data = ['status' => 1];
        } else {
            $data = ['status' => 0, 'message' => $userModel->getErrorMessage()];
        }
        return $response->withJson($data, 200);
    }
    
    /**
     * Залогинивание пользователя
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $args
     * @return type
     */
    public function login(RequestInterface $request, ResponseInterface $response, $args)
    {
        $allPostPutVars = $request->getParsedBody();
        $userModel = new UserModel();
        session_start();
        $result = $userModel->authUser($allPostPutVars['login'] ,$allPostPutVars['pass']);
        if ($result && !$this->isAuth()) {
            $_SESSION['userId'] = $userModel->getUserId();
            $data = ['status' => 1];
        } else {
            $message = $userModel->getErrorMessage();
            if ($this->isAuth($userModel->getUserId())) {
                $message = 'Пользователь уже авторизован';
            }
            $data = ['status' => 0, 'message' => $message];
        }
        return $response->withJson($data, 200);
    }
    
    /**
     * Разлогинивание пользователя
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $args
     * @return string
     */
    public function logout(RequestInterface $request, ResponseInterface $response, $args)
    {
        session_start();
        unset($_SESSION['userId']);
        session_destroy();
        return $response->withJson(['status' => 1], 200);
    }
}
