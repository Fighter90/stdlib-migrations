<?php

namespace Migrations\Tools;

use Migrations\Tools\AccessMigrationFactory as Factory;

/**
 * Class AccessMigrationTrait
 *
 * @package Migrations\Tools
 */
trait AccessMigrationTrait
{
    /**
     * Выполнение миграции
     */
    public function processUp()
    {
        Factory::under()->getOrCreate();
        Factory::reset();
    }
    
    /**
     * Откат миграции
     */
    public function processDown()
    {
        Factory::under()->remove();
        Factory::reset();
    }
    
    /**
     * @param string $name
     * @param array $arguments
     * @param bool $remove
     * @return $this
     */
    public function set(string $name, array $arguments, $remove = NULL)
    {
        Factory::$name()->setData($arguments);
    
        if (is_bool($remove)) {
            Factory::$name()->setRemove($remove);
        }
        
        return $this;
    }
}