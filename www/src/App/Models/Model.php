<?php
namespace App\Models;

use App\Db\Connect;

class Model
{

    /**
     * Подключение к БД через PDO
     * @var object PDO 
     */
    protected $connection;

    /**
     * Сообщение об ошибке
     * @var string 
     */
    protected $errorMessage = '';

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->connection = Connect::getInstance()->getConnection();
    }

    /**
     * Получение сообщения об ошибке
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
