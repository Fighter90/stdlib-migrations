<?php

namespace Migrations\Tools;

use Migrations\Tools\AccessMigrationFactory as Factory;

/**
 * Class ApiUnderGui
 *
 * @package Migrations\Tools
 */
class ApiUnderGui extends AbstractAccessMigration
{
    /**
     * @var string
     */
    const ALIAS = 'under';
    
    /**
     * Таблица связей api и gui
     *
     * @var string
     */
    protected $table = 'api_under_gui';
    
    /**
     * Условия для поиска уникалной записи
     *
     * @var array
     */
    protected $condition = [
        'gui_resource_id',
        'api_resource_id'
    ];
    
    /**
     * @var bool
     */
    protected $remove = true;
    
    /**
     * @param array $data
     * @return array|bool
     */
    public function getOrCreate(array $data = [])
    {
        if ( ! empty($data) || ! empty($this->getData())) {
            return parent::getOrCreate($data);
        }
        
        $api  = Factory::api()->getOrCreate();
        $guis = Factory::gui()->getHaystack();
        
        foreach ($guis as $data) {
            $gui = Factory::gui()->getOrCreate($data);
            
            parent::getOrCreate([
                'gui_resource_id' => $gui['id'],
                'api_resource_id' => $api['id'],
                'is_main'         => true
            ]);
        }
        
        return true;
    }
    
    /**
     * @param array $data
     */
    public function remove(array $data = [])
    {
        if ( ! empty($data) || ! empty($this->getData())) {
            parent::remove($data);
        }
        
        $api  = Factory::api()->getOrCreate();
        $guis = Factory::gui()->getHaystack();
        
        foreach ($guis as $data) {
            $gui = Factory::gui()->getOrCreate($data);
            
            parent::remove([
                'gui_resource_id' => $gui['id'],
                'api_resource_id' => $api['id']
            ]);
            
            Factory::gui()->remove($gui);
        }
        
        Factory::api()->remove($api);
    }
}