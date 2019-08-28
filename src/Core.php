<?php

namespace App;

use App\Database\Driver;
use App\Support\SlightlyBigFlipApi;

/**
 * Singleton Application Core
 */
class Core
{

    /** @var string */
    public $path;

    /** @var float */
    public $startTime;

    /** @var array */
    public $arguments;

    /** @var Driver */
    public $database;

    /** @var SlightlyBigFlipApi */
    public $api;

    /**
     * Instantiate class
     *
     * @param string $path
     */
    public function __construct()
    {
        $this->setDatabase(new Driver);

        $this->setArguments($GLOBALS['argv']);

        $this->setApi(new SlightlyBigFlipApi);
    }

    /**
     * App terminate
     */
    public function __destruct()
    {
        echo PHP_EOL;
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param string $path
     * @return  self
     */ 
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of startTime
     */ 
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set the value of startTime
     *
     * @param float $startTime
     * @return  self
     */ 
    public function setStartTime(float $startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get the value of database
     */ 
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the value of database
     *
     * @return  self
     */ 
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Get the value of arguments
     */ 
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * get app arg value by index
     *
     * @param int $index
     * @param mixed $default
     * @return mixed
     */
    public function getArgument(int $index, $default = null) {
        if(!isset($this->arguments[$index])) {
            return $default;
        }

        return $this->arguments[$index];
    }

    /**
     * Set the value of arguments
     *
     * @return  self
     */ 
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Get the value of api
     */ 
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set the value of api
     *
     * @return  self
     */ 
    public function setApi($api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * read user input
     *
     * @param string $prompt
     * @param string|null $default
     * @return string|null
     */
    public function readInput($prompt, $default = null) {
        $input = readline($prompt);
        
        if(!$input) {
            return $default;
        }

        return $input;
    }
}
