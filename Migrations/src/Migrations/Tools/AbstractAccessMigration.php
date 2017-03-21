<?php

namespace Migrations\Tools;

use yii\db\Migration;
use yii\db\Query;

/**
 * Class AccessMigration
 *
 * @package Migrations\Tools
 */
abstract class AbstractAccessMigration extends Migration
{
    /**
     * @var string
     */
    protected $table;
    
    /**
     * @var array
     */
    protected $condition = [];
    
    /**
     * @var Query
     */
    private $query;
    
    /**
     * @var array
     */
    private $data = [];
    
    /**
     * @var bool
     */
    protected $remove = false;
    
    /**
     * AbstractAccessMigration constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->query = new Query();
    }
    
    /**
     * @param array $data
     * @return array|bool
     */
    public function getOrCreate(array $data = [])
    {
        if ( ! empty($data)) {
            $this->setData($data, true);
        }
        
        if ( ! $record = $this->get()) {
            return $this->create();
        }
        
        return $record;
    }
    
    /**
     * @return array|bool
     */
    private function create()
    {
        $this->insert($this->table, $this->data);
        
        return $this->get();
    }
    
    /**
     * @return array|bool
     */
    private function get()
    {
        return $this->query->select('*')->from($this->table)->where($this->getCondition())->one();
    }
    
    /**
     * @param array $data
     */
    public function remove(array $data = [])
    {
        if ( ! $this->isRemove()) {
            return;
        }
        
        if ( ! empty($data)) {
            $this->setData($data, true);
        }
        
        $this->delete($this->table, $this->getCondition());
    }
    
    /**
     * @param array $data
     */
    public function selfUpdate(array $data)
    {
        parent::update($this->table, $data, $this->getCondition());
    }
    
    /**
     * @param array $data
     * @param bool $merge
     */
    public function setData(array $data, $merge = false)
    {
        $this->data = ($merge)
            ? array_merge($this->data, $data)
            : $data;
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return (array_key_exists($key, $this->getData()));
    }
    
    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getValue($key, $default = NULL)
    {
        if ($this->hasKey($key)) {
            return $this->data[$key];
        }
        
        return $default;
    }
    
    /**
     * @param bool $remove
     */
    public function setRemove(bool $remove)
    {
        $this->remove = $remove;
    }
    
    /**
     * Удалять элемент при откате пишрации
     *
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove;
    }
    
    /**
     * @return array
     */
    public function getCondition(): array
    {
        $condition = [];
        
        foreach ($this->condition as $key) {
            $condition[$key] = $this->getValue($key);
        }
        
        return $condition;
    }
}