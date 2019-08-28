<?php

namespace App\Database;

use PDO;
use PDOStatement;
use RuntimeException;
use App\Concerns\HasConfig;

class Driver {

    use HasConfig;
    
    /** @var self */
    public $connection;

    /** @var int */
    public $rowCount;

    /**
     * Instantiate 
     */
	function __construct() {
        $this->bootConfig();
        $this->bootConnection();
    }
    
    /**
     * on destroy
     */
	function __destruct(){
		$this->connection = null;
    }

    /**
     * get config path
     * 
     * @return string|null
     */
    public function configPath() {
        return realpath(__DIR__ . '/../../config/database.php');

    } 

    /**
     * boot db connection
     *
     * @return void
     */
    protected function bootConnection() {
        if(!isset($this->connection)) {
            $dsn = implode(null, [
                "{$this->config['driver']}:",
                "host={$this->config['host']};",
                "dbname={$this->config['database']}",
            ]);
    
            $this->connection = new PDO($dsn, $this->config['user'], $this->config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }

    /**
     * Run query
     *
     * @param string $query
     * @param array|null $parameters
     * @return PDOStatement
     */
    public function query($query, $parameters = null) {
        if(!isset($this->connection)) {
            throw new RuntimeException("No connection...");
        }

        $query = $this->connection->prepare($query);
        $query->execute($parameters);

        $this->setRowCount($query->rowCount());

        return $query;
    }
    
	public function fetch($query, $parameters = null){
        return $this->query($query, $parameters)->fetch();
    }
    
	public function fetchAll($query, $parameters = null) {
        return $this->query($query, $parameters)->fetchAll();
    }
    
	public function tableExists($table){
        $this->query("SHOW TABLES LIKE '$table'");
        return ($this->getRowCount() > 0) ? true : false;
	}


    /**
     * Get the value of rowCount
     */ 
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Set the value of rowCount
     *
     * @param int $rowCount
     * @return  self
     */ 
    public function setRowCount(int $rowCount)
    {
        $this->rowCount = $rowCount;

        return $this;
    }

    /**
     * Get the value of connection
     */ 
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the value of connection
     *
     * @return  self
     */ 
    public function setConnection(self $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}
