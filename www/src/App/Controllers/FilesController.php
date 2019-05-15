<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use App\Models\FileModel;

class FilesController extends PublicController
{

    /**
     * Конструктор
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Загрузка файла
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return type
     */
    public function fileUpload(RequestInterface $request, ResponseInterface $response)
    {
        if ($this->isAuth()) {
            $fileModel = new FileModel();
            if ($fileModel->saveFile($request)) {
                $data = ['status' => 1];
            } else {
                $data = ['status' => 0, 'message' => $fileModel->getErrorMessage()];
            }
        } else {
            $data = ['status' => 0, 'message' => 'Пользователь не авторизован'];
        }
        return $response->withJson($data, 200);
    }

    /**
     * Получение списка файлов
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $args
     * @return type
     */
    public function filesList(RequestInterface $request, ResponseInterface $response)
    {
        if ($this->isAuth()) {
            $fileModel = new FileModel();
            $data = ['status' => 1, 'files' => $fileModel->geFilesList()];
        } else {
            $data = ['status' => 0, 'message' => 'Пользователь не авторизован'];
        }
        return $response->withJson($data, 200);
    }

    /**
     * Получение файла
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $args
     * @return type
     */
    public function getFile(RequestInterface $request, ResponseInterface $response)
    {
        if ($this->isAuth() && !is_null($request->getQueryParams()['id'])) {
            $fileModel = new FileModel();
            return $fileModel->getFile($request, $response);
        } else {
            return $response->withStatus(404);
        }
    }

    /**
     * Обновление файла
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return type
     */
    public function updateFile(RequestInterface $request, ResponseInterface $response)
    {
        if ($this->isAuth() && !is_null($request->getQueryParams()['id'])) {
            $fileModel = new FileModel();
            if ($fileModel->updateFile($request)) {
                $data = ['status' => 1];
            } else {
                $data = ['status' => 0, 'message' => $fileModel->getErrotMessage()];
            }
        } else {
            $data = ['status' => 0, 'message' => 'Пользователь не авторизован'];
        }
        return $response->withJson($data, 200);
    }
}
