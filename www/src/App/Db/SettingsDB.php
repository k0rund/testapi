<?php

namespace App\Db;

/**
 * Description of SettingsDB
 *
 * @author k0rund
 */
class SettingsDB 
{
    /**
     * Настройки для БД
     * @var type 
     */
    private $settings;
    
    /**
     *
     * @var Object SettingsDB
     */
    private static $_instance;

    public function __construct() 
    {
        $this->settings = (require __DIR__ . '/../../settings.php')['db'];
    }
    
    public function getSettings (): array
    {
        return $this->settings;
    }
    
    public function setSettings (array $settings = null)
    {
        $this->settings = $settings;
    }
    
    private function __clone()
    {
        
    }

    private function __wakeup()
    {
        
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
