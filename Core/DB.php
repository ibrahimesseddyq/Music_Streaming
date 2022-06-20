<?php

namespace Core;

use PDO;
use App\Config;


class DB {

	private $dbh;
	private $error;
	private $stmt;

	public function __construct()
	{
		$dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . 
				Config::DB_NAME . ';charset=utf8';
		
		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		try {
			$this->dbh = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD, $options);
		} 
		catch ( PDOException $e ) {
			$this->error = $e->getMessage();
		}

	}

	public function query($query)
	{
		$this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			 switch (true) {
			   case is_int($value):
					 $type = PDO::PARAM_INT;
					 break;
			   case is_bool($value):
					 $type = PDO::PARAM_BOOL;
					 break;
			   case is_null($value):
					 $type = PDO::PARAM_NULL;
					 break;
				   default:
					 $type = PDO::PARAM_STR;
			 }
	   }
	   $this->stmt->bindValue($param, $value, $type);
   }

	
	public function execute()
	{
		return $this->stmt->execute();
	}
	
	public function resultSet()
	{
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}

	public function single()
	{
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_OBJ);
	}

}