<?php

namespace Migrations\Tools;

/**
 * Class GuiResources
 *
 * @package Migrations\Tools
 */
class GuiResources extends AbstractAccessMigration
{
    /**
     * @var string
     */
    const ALIAS = 'gui';
    
    /**
     * Таблица GUI ресурсов
     *
     * @var string
     */
    protected $table = 'gui_resources';
    
    /**
     * Условия для поиска уникалной записи
     *
     * @var array
     */
    protected $condition = [
        'component',
        'route_id',
        'view_script',
        'date_removed'
    ];
    
    /**
     * @var array
     */
    protected $haystack = [];
    
    /**
     * @var bool
     */
    protected $remove = true;
    
    /**
     * @param array $data
     * @param bool $merge
     */
    public function setData(array $data, $merge = false)
    {
        if (array_search($data, $this->haystack) === false) {
            $this->haystack[] = $data;
        }
    }
    
    /**
     * @return array
     */
    public function getHaystack()
    {
        return $this->haystack;
    }
    
    /**
     * @param array $data
     * @param bool $single
     * @return array|bool
     */
    public function getOrCreate(array $data = [], $single = true)
    {
        if ($single) {
            parent::setData($data);
            
            return parent::getOrCreate();
        }
        
        foreach ($this->getHaystack() as $data) {
            parent::setData($data);
            parent::getOrCreate();
        }
        
        return true;
    }
}