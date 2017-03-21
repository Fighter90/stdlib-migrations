<?php

use Migrations\Tools\ApiResources;
use Migrations\Tools\ApiResourcesGroups;
use Migrations\Tools\GuiResources;
use Migrations\Tools\MigrationTpl;

class m161208_122556_template_access_migration extends MigrationTpl
{
    /**
     * Инициализация
     */
    public function init()
    {
        parent::init();
        
        $this->set(ApiResourcesGroups::ALIAS, [
            'name' => 'Group 1',
        ])->set(ApiResources::ALIAS, [
            'component'   => 'hub',
            'class_name'  => 'class 1',
            'method'      => 'index 1',
            'manage_mode' => 0,
            'access_type' => 1
        ])->set(GuiResources::ALIAS, [
            'view_script' => 'view 1',
            'component'   => 'hub',
            'icon_cls'    => 'right-icon new-icon x-fa fa-home',
            'route_id'    => 'main 1',
            'title'       => 'Главная 1',
            'in_menu'     => true
        ])->set(GuiResources::ALIAS, [
            'view_script' => 'view 2',
            'component'   => 'hub',
            'icon_cls'    => 'right-icon new-icon x-fa fa-home',
            'route_id'    => 'main 2',
            'title'       => 'Главная 2',
            'in_menu'     => true
        ]);
    }
    
    /**
     * Накат миграций
     */
    public function up()
    {
        $this->processUp();
    }
    
    /**
     * Откат миграций
     */
    public function down()
    {
        $this->processDown();
    }
}