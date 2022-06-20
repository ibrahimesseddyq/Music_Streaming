<?php

namespace Core;


use PDO;
use App\Config;
use Core\DB;

abstract class Model
{
    protected $db;

    public function __construct()
    {

       $this->db = new DB;
	}

    public function findByField($data = array()) 
    {
        $field = array_keys($data)[0];

        $table = static::getTable();
        
        $this->db->query("SELECT * FROM {$table} WHERE {$field} = :data");

        $this->db->bind(':data', $data[$field]);

        $row = $this->db->single();

        return $row;


    }

    public function itemExists($data = array()) 
    {
        if ($this->findByField($data)){
            return true;
        }
        return false;
    }

    public static function getTable()
    {
        $className = get_called_class();

        $table = strtolower(substr($className, strrpos($className, '\\') + 1))."s";
        
        return $table;
    }

}
