<?php
namespace App\Controllers;

class PublicController
{
    /**
     * Проверка залогинен ли пользователь в системе
     * @return bool
     */
    protected function isAuth(): bool
    {
        return isset($_SESSION['userId']) ? true : false;
    }
}
