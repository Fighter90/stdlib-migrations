<?php

namespace Migrations\Tools;

use yii\console\Exception;
use yii\db\Query;

/**
 * Class AvailableGuiMigrationTrait
 *
 * @property array $api_resources
 * @property array $api_resources_groups
 * @property array $gui_resources
 * @package Application\Utils
 */
trait AvailableGuiMigrationTrait
{
    /**
     * Группа api ресурса
     *
     * @var array
     */
    protected $api_group = [];
    
    /**
     * Api ресурс
     *
     * @var array
     */
    protected $api = [];
    
    /**
     * Gui Ресурс
     *
     * @var array
     */
    protected $gui = [];
    
    /**
     * Пользователь
     *
     * @var array
     */
    protected $user = [];
    
    /**
     * Таблицы
     *
     * @var array
     */
    protected $tables = [
        'api_group' => 'api_resources_groups',
        'api'       => 'api_resources',
        'gui'       => 'gui_resources',
        'user'      => 'users',
        'acl'       => 'user_acls',
        'under'     => 'api_under_gui',
    ];
    
    /**
     * Инициализация
     *
     * @return $this
     */
    public function init()
    {
        $this->validation();
        
        $this->api_group = $this->getApiGroup();
        $this->api       = $this->getApi();
        $this->gui       = $this->getGui();
        $this->user      = $this->getUser();
        
        return $this;
    }
    
    /**
     * Проверка наличия обязательных свойств
     *
     * @throws Exception
     */
    protected function validation()
    {
        if ( ! property_exists($this, 'api_resources')) {
            throw new Exception('Property "api_resources" is required!');
        }
        
        if ( ! array_key_exists('group_id', $this->api_resources) && ! property_exists($this, 'api_resources_groups')) {
            throw new Exception('Property "api_resources_groups" is required!');
        }
        
        if ( ! property_exists($this, 'gui_resources')) {
            throw new Exception('Property "gui_resources" is required!');
        }
    }
    
    /**
     * Получаем группу api ресурса
     *
     * @return array|bool
     */
    protected function getApiGroup()
    {
        $query = new Query();
        
        if (array_key_exists('group_id', $this->api_resources)) {
            $where = ['id' => $this->api_resources['group_id']];
        } elseif (array_key_exists('id', $this->api_resources_groups)) {
            $where = ['id' => $this->api_resources_groups['id']];
        } else {
            $where = ['name' => $this->api_resources_groups['name']];
        }
        
        $where['date_removed'] = NULL;
        
        return $query->select('id')->from($this->getTable('api_group'))->where($where)->one();
    }
    
    /**
     * Получаем api ресурс
     *
     * @return array|bool
     */
    protected function getApi()
    {
        $query = new Query();
        
        return $query->select('id')->from($this->getTable('api'))->where([
            'component'    => $this->api_resources['component'],
            'class_name'   => $this->api_resources['class_name'],
            'method'       => $this->api_resources['method'],
            'date_removed' => NULL
        ])->one();
    }
    
    /**
     * Получаем gui ресурс
     *
     * @return array|bool
     */
    protected function getGui()
    {
        $query = new Query();
        
        return $query->select('id')->from($this->getTable('gui'))->where([
            'component'    => $this->gui_resources['component'],
            'route_id'     => $this->gui_resources['route_id'],
            'view_script'  => $this->gui_resources['view_script'],
            'date_removed' => NULL
        ])->one();
    }
    
    /**
     * Поручаем пользователя
     *
     * @return array|bool
     */
    protected function getUser()
    {
        $query = new Query();
        
        return $query->select('id')->from($this->getTable('user'))->where([
            'username' => 'patrinat@gmail.com'
        ])->one();
    }
    
    /**
     * Получаем права пользователя на api ресурсы
     *
     * @param array $user
     * @param array $api
     * @return array|bool
     */
    protected function getAcl(array $user, array $api)
    {
        $query = new Query();
        
        return $query->select('id')->from($this->getTable('acl'))->where([
            'user_id'      => $user['id'],
            'resource_id'  => $api['id'],
            'date_removed' => NULL
        ])->one();
    }
    
    /**
     * Получаем связанный api и gui ресурс
     *
     * @param array $gui
     * @param array $api
     * @return array|bool
     */
    protected function getUnder(array $gui, array $api)
    {
        $query = new Query();
        
        return $query->select('id')->from($this->getTable('under'))->where([
            'gui_resource_id' => $gui['id'],
            'api_resource_id' => $api['id']
        ])->one();
    }
    
    /**
     * Получение таблицы
     *
     * @param string $name
     * @return string
     * @throws Exception
     */
    protected function getTable($name)
    {
        if (array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }
        
        throw new Exception('Table "' . $name . '" not found!');
    }
    
    /**
     * Выполнение миграций
     */
    public function processUp()
    {
        if ( ! $this->api_group) {
            $this->insert($this->getTable('api_group'), $this->api_resources_groups);
            $this->api_group = $this->getApiGroup();
        }
        
        if ( ! $this->api) {
            if ( ! array_key_exists('group_id', $this->api_resources)) {
                $this->api_resources['group_id'] = $this->api_group['id'];
            }
            
            $this->insert($this->getTable('api'), $this->api_resources);
            $this->api = $this->getApi();
        }
        
        if ( ! $this->gui) {
            $this->insert($this->getTable('gui'), $this->gui_resources);
            $this->gui = $this->getGui();
        }
        
        if ($this->user && ! $this->getAcl($this->user, $this->api)) {
            $this->insert($this->getTable('acl'), [
                'user_id'     => $this->user['id'],
                'resource_id' => $this->api['id']
            ]);
        }
        
        if ( ! $this->getUnder($this->gui, $this->api)) {
            $this->insert($this->getTable('under'), [
                'gui_resource_id' => $this->gui['id'],
                'api_resource_id' => $this->api['id'],
                'is_main'         => true
            ]);
        }
    }
    
    /**
     * Откат миграций
     */
    public function processDown()
    {
        $this->delete($this->getTable('acl'), ['user_id' => $this->user['id'], 'resource_id' => $this->api['id']]);
        $this->delete($this->getTable('under'), [
            'gui_resource_id' => $this->gui['id'],
            'api_resource_id' => $this->api['id']
        ]);
        $this->delete($this->getTable('api'), ['id' => $this->api['id']]);
        $this->delete($this->getTable('gui'), ['id' => $this->gui['id']]);
        $this->delete($this->getTable('api_group'), ['id' => $this->api_group['id']]);
    }
}
