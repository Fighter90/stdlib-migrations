<?php

namespace Migrations\Tools;

/**
 * Class ApiResourcesGroups
 *
 * @package Migrations\Tools
 */
class ApiResourcesGroups extends AbstractAccessMigration
{
    /**
     * @var string
     */
    const ALIAS = 'api_group';
    
    /**
     * Таблица групп API ресурсов
     */
    protected $table = 'api_resources_groups';
    
    /**
     * Условия для поиска уникалной записи
     *
     * @var array
     */
    protected $condition = [
        'name',
        'date_removed'
    ];
    
    /**
     * @var bool
     */
    protected $remove = false;
}