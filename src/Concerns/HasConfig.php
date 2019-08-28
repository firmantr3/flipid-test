<?php 

namespace App\Concerns;

trait HasConfig {

    /** @var array */
    protected $config;

    /**
     * Initialize config
     */
    protected function bootConfig() {
        if($this->configPath() && !isset($this->config)) {
            $this->config = require($this->configPath());
        }
    }

    /**
     * get config path
     * 
     * @return string|null
     */
    public function configPath() {
        return null;
    } 

}
