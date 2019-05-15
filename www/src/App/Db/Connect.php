<?php
namespace App\Db;

use App\Db\SettingsDB;

class Connect
{

    /**
     *
     * @var Object Connect
     */
    private static $_instance;
    /**
     * Объект PDO
     * @var object 
     */
    private $_pdo;
    /**
     * Пользователь для подключения к БД
     * @var string 
     */
    private $_pdoUser = '';
    /**
     * Пароль для подключения к БД
     * @var string 
     */
    private $_pdoPassword = '';
    /**
     * Режим 
     * @var array 
     */
    private $_pdoPrm = [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];

    /**
     * Constructor
     */
    private function __construct()
    {
        $settings = SettingsDB::getInstance()->getSettings();
        $this->_pdoUrl = 'mysql:host=' . $settings['host'] . ';dbname=' . $settings['dbname'] . ';charset=utf8';
        $this->_pdoUser = $settings['user'];
        $this->_pdoPassword = $settings['pass'];
        $this->_pdo = new \PDO($this->_pdoUrl, $this->_pdoUser, $this->_pdoPassword, $this->_pdoPrm);
    }

    private function __clone()
    {
        
    }

    private function __wakeup()
    {
        
    }

    /**
     * Создание экземпляра класса
     * @param array $settings
     * @return type
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Получение объекта PDO
     * @return type
     */
    public function getConnection()
    {
        return $this->_pdo;
    }
}
