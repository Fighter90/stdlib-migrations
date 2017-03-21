<?php
/**
 * Created by PhpStorm.
 * User: tashik
 * Date: 23.08.16
 * Time: 12:41
 */

namespace Migrations\Tools;

use yii\db\Migration as ParentMigration;

class MigrationTpl extends ParentMigration
{
    use AccessMigrationTrait;

    const SET = 'SET';
    const DROP = 'DROP';

    protected $_dbUsers = array();
    protected $_migrationParams = array();

    public function __construct(array $config, array $params = null)
    {
        parent::__construct($config);
        $dbUsers = (isset($params['dbUsers'])) ? $params['dbUsers'] : array();
        if (!empty($dbUsers)) {
            $this->_dbUsers = $dbUsers;
        }
        $this->_migrationParams = $params;
    }

    public function createTable($table, $columns, $comment = '', $options = null)
    {
        parent::createTable($table, $columns, $options);
        if (!empty($comment)) {
            $this->addTableComment($table, $comment);
        }

        $sequence = null;

        foreach($columns as $name => $type) {
            if (stripos($type, 'pk') !== false) {
                $sequence = $table.'_'.$name.'_seq';
            }
        }

        $this->grantTablePermissions($table, $sequence);

    }

    public function grantTablePermissions($table, $sequence = null) {
        $dbUsers = $this->_dbUsers;

        if (empty($dbUsers)) {
            return;
        }

        if (isset ($dbUsers['db_owner'])) {
            $this->execute(sprintf('ALTER TABLE "'.$table.'" OWNER TO "%s"', $dbUsers['db_owner']));
            if ($sequence) {
                $this->execute(sprintf('ALTER SEQUENCE "'.$sequence.'" OWNER TO "%s"', $dbUsers['db_owner']));
            }
        }

        if (isset($dbUsers['server_user'])) {
            $this->execute(sprintf('GRANT ALL ON TABLE '.$table.' TO "%s"', $dbUsers['server_user']));
            if ($sequence) {
                $this->execute(sprintf('GRANT ALL ON SEQUENCE '.$sequence.' TO "%s"', $dbUsers['server_user']));
            }
        }

        if (isset($dbUsers['rw_group'])) {
            $this->execute(sprintf('GRANT ALL ON TABLE '.$table.' TO "%s"', $dbUsers['rw_group']));
            if ($sequence) {
                $this->execute(sprintf('GRANT ALL ON SEQUENCE '.$sequence.' TO "%s"', $dbUsers['rw_group']));
            }
        }
        if (isset($dbUsers['ro_group'])) {
            $this->execute(sprintf('GRANT SELECT ON TABLE '.$table.' TO "%s"', $dbUsers['ro_group']));
            if ($sequence) {
                $this->execute(sprintf('GRANT SELECT ON SEQUENCE '.$sequence.' TO "%s"', $dbUsers['ro_group']));
            }
        }
        echo " Table ".$table.($sequence ? "AND sequence ".$sequence : "")." ownership is established. Permissions are granted\n";
    }

    public function addTableComment($table, $comment)
    {
        echo "    > add comment '$comment' to table $table ...";
        $time = microtime(true);
        $this->db->createCommand("COMMENT ON TABLE $table IS '$comment'")->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addColumn($table, $column, $type, $comment = '')
    {
        parent::addColumn($table, $column, $type);
        if (!empty($comment)) {
            $this->addColumnComment($table, $column, $comment);
        }
    }

    public function addColumnComment($table, $column, $comment)
    {
        echo "    > add comment '$comment' in table $table to $column ...";
        $time = microtime(true);
        $this->getDbConnection()->createCommand("COMMENT ON COLUMN $table.$column IS '$comment'")->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function alterNotNull($table, $column, $action = self::SET)
    {
        echo "    > $action not null in table $table to $column ...";
        $time = microtime(true);
        $this->getDbConnection()->createCommand("ALTER TABLE $table ALTER COLUMN $column $action NOT NULL")->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function alterNull($table, $column, $action = self::DROP)
    {
        echo "    > $action null in table $table to $column ...";
        $time = microtime(true);
        $this->getDbConnection()->createCommand("ALTER TABLE $table ALTER COLUMN $column $action NOT NULL")->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function alterDefault($table, $column, $action = self::SET, $default = null)
    {
        echo "    > $action default " . ($default !== null ? "$default " : '') . "in table $table to $column ...";
        $time = microtime(true);
        $this->getDbConnection()
            ->createCommand("ALTER TABLE $table ALTER COLUMN $column $action DEFAULT " . ($default !== null ? $default : ''))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function getDbConnection() {
        return $this->db;
    }
}
