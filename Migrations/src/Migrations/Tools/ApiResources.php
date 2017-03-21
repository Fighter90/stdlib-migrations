<?php

namespace Migrations\Tools;

use Migrations\Tools\AccessMigrationFactory as Factory;

/**
 * Class ApiResources
 *
 * @package Migrations\Tools
 */
class ApiResources extends AbstractAccessMigration
{
    /**
     * @var string
     */
    const ALIAS = 'api';
    
    /**
     * Таблица API ресурсов
     *
     * @var string
     */
    protected $table = 'api_resources';
    
    /**
     * @var bool
     */
    protected $remove = true;
    
    /**
     * Условия для поиска уникалной записи
     *
     * @var array
     */
    protected $condition = [
        'component',
        'class_name',
        'method',
        'date_removed'
    ];
    
    /**
     * @param array $data
     * @return array|bool
     */
    public function getOrCreate(array $data = [])
    {
        $group = Factory::api_group()->getOrCreate();
        
        if (empty($this->getValue('group_id'))) {
            $data['group_id'] = $group['id'];
        }
        
        $api = parent::getOrCreate($data);
    
        if ($api['group_id'] != $group['id']) {
            $this->selfUpdate($data);
        }
        
        return parent::getOrCreate($data);
    }
    
    /**
     * @param array $data
     */
    public function remove(array $data = [])
    {
        parent::remove($data);
        
        Factory::api_group()->remove();
    }
}