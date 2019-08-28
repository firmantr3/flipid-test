<?php 

namespace App\Database;

use App\Database\Driver;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Migration {

    /** @var Driver */
    protected $driver;

    /** @var array */
    protected $migrations = [];

    /** @var array */
    protected $logs = [];

    /**
     * Get the value of driver
     */ 
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set the value of driver
     *
     * @return  self
     */ 
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function resetTables() {
        $tables = $this->driver->query('show tables')->fetchAll();

        $flattenTables = new RecursiveIteratorIterator(new RecursiveArrayIterator($tables));

        foreach ($flattenTables as $key => $table) {
            $this->driver->query("drop table {$table}");

            array_push($this->logs, "Dropped table {$table}");
        }
    }

    /**
     * Set the value of migrationSqls
     *
     * @return  self
     */ 
    public function addMigration($name, $sqlCommand)
    {
        array_push($this->migrations, [
            'name' => $name,
            'sql' => $sqlCommand
        ]);

        return $this;
    }

    /**
     * Run migrations command
     *
     * @return array
     */
    public function run() { 
        $this->driver->beginTransaction();

        try{
            $this->resetTables();

            foreach ($this->migrations as $key => $migration) {
                $this->driver->query($migration['sql']);
                
                array_push($this->logs, "Migrated: {$migration['name']}");
            }

            $this->driver->commit();
        }
        catch (Exception $e) {
            $this->driver->rollBack();
        }

        return $this->logs;
    }
}
