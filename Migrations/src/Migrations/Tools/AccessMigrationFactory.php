<?php

namespace Migrations\Tools;

use Exception;

/**
 * Class AccessMigrationFactory
 * @method static ApiResources api()
 * @method static ApiResourcesGroups api_group()
 * @method static GuiResources gui()
 * @method static ApiUnderGui under()
 *
 * @package Migrations\Tools
 */
class AccessMigrationFactory
{
    /**
     * @var array
     */
    private static $map = [
        ApiResources::ALIAS       => ApiResources::class,
        ApiResourcesGroups::ALIAS => ApiResourcesGroups::class,
        GuiResources::ALIAS       => GuiResources::class,
        ApiUnderGui::ALIAS        => ApiUnderGui::class
    ];
    
    /**
     * @var array
     */
    protected static $data = [];
    
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if ( ! array_key_exists($name, self::$data)) {
            if ( ! array_key_exists($name, self::$map)) {
                throw new Exception("Calling unknown method: $name()");
            }
            
            $class = self::$map[$name];
            
            if ( ! class_exists($class, true)) {
                throw new Exception("Unable to load class: $class");
            }
            
            self::$data[$name] = new $class($arguments);
        }
        
        return self::$data[$name];
    }
    
    /**
     * Сброс фабрики в начальное состояние
     */
    public static function reset()
    {
        self::$data = [];
    }
}