<?php
namespace App\Models;

use App\Db\Connect;

class UserModel extends Model
{

    private $resultSql;

    /**
     * Создание новго пользователя.
     * @param string $login
     * @param string $pass
     * @return bool
     */
    public function createUser(string $login, string $pass): bool
    {
        $result = false;
        $isFind = $this->findUserByLogin($login);
        if ($isFind) {
            $this->errorMessage = 'Пользватель с таким логином уже существует';
            return $result;
        }
        
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('INSERT INTO users (login, pass_hash) VALUES (:login, :hash)');
        $result = $stmt->execute(['login' => $login, 'hash' => password_hash($pass, PASSWORD_DEFAULT)]);
        if (!$result) {
            $this->errorMessage = 'Не удалось создать нового пользователя';
        }
        return $result;
    }
    
    /**
     * Проверка на существование пользвателя с таким логином в БД
     * @param string $login
     * @return bool
     */
    private function findUserByLogin (string $login): bool
    {
        $isFind = false;
        $connetion = Connect::getInstance()->getConnection();
        $stmt = $connetion->prepare('SELECT id, login, pass_hash FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        foreach ($stmt as $row) {
            $this->resultSql = $row;
            $isFind = true;
            break;
        }
        return $isFind;
    }

    public function authUser(string $login, string $pass): bool
    {
        $result = false;
        $isFind = $this->findUserByLogin($login);
        if (!$isFind) {
            $this->errorMessage = 'Ошибка авторизации';
            return $result;
        }
        $pass_hash = $this->resultSql['pass_hash'];
        if (password_verify($pass, $pass_hash)) {
            $result = true;
        } else {
            $this->errorMessage = 'Ошибка авторизации';
        }
        return $result;
    }
    public function getUserId()
    {
        return $this->resultSql['id'];
    }
}
