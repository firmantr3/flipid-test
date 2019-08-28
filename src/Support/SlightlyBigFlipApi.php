<?php

namespace App\Support;

use App\Concerns\HasConfig;
use RuntimeException;

class SlightlyBigFlipApi {

    use HasConfig;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $cacert;

    /**
     * instantiate class
     */
    public function __construct()
    {
        $this->bootConfig();

        $this->setBaseUrl($this->config['host']);

        $this->setCacert($this->rootPath('/cacert.pem'));
    }

    /**
     * get app root path
     *
     * @param string|null $path
     * @return string
     */
    protected function rootPath($path = null) {
        return realpath(__DIR__ . '/../../') . $path;
    }

    /**
     * get API url
     *
     * @param string|null $url
     * @return string
     */
    protected function getUrl($url = null) {
        return "{$this->baseUrl}{$url}";
    }

    /**
     * get config path
     * 
     * @return string|null
     */
    public function configPath() {
        return $this->rootPath('/config/slightlyBigFlipApi.php');
    } 

    /**
     * Call api 
     *
     * @param string $url
     * @param string $method
     * @param array $payload
     * @return array
     */
    public function call($url, $method = 'GET', $payload = []) {
        // Initialize session and set URL.
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $this->getUrl($url));

        // Set so curl_exec returns the result instead of outputting it.
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);

        // Set up authorization
        curl_setopt($channel, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: basic ' . base64_encode("{$this->config['key']}:"),
        ]);

        if($method === 'POST') {
            // post_data
            curl_setopt($channel, CURLOPT_POST, true);
            curl_setopt($channel, CURLOPT_POSTFIELDS, http_build_query($payload));
        }

        // Set up ssl
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($channel, CURLOPT_CAINFO, $this->getCacert());

        curl_setopt($channel, CURLOPT_VERBOSE, true);
        
        // Get the response and close the channel.
        $response = curl_exec($channel);
        curl_close($channel);

        echo PHP_EOL . PHP_EOL;

        return json_decode($response, true);
    }
    
    /**
     * call api by route name
     *
     * @param string $route
     * @param array $arguments
     * @param array $payload
     * @return array
     */
    public function callRoute($route, $arguments = [], $payload = []) {
        list($method, $routeUrl) = $this->config['routes'][$route];

        $routeUrl = $this->parseRoute($routeUrl, $arguments);
        
        return $this->call($routeUrl, $method, $payload);
    }

    /**
     * Call create disbursement API
     *
     * @param array $payload
     * @return array
     */
    public function createDisbursement($payload) {
        return $this->callRoute('post_disbursement', [], $payload);
    }

    /**
     * Call show disbursement API
     *
     * @param array $arguments
     * @return array
     */
    public function showDisbursement($arguments) {
        return $this->callRoute('show_disbursement', $arguments);
    }

    /**
     * parse route url
     *
     * @param string $routeUrl
     * @param array $arguments
     * @return string
     */
    protected function parseRoute($routeUrl, $arguments) {
        // search for route parameter on the route url
        preg_match_all('/{.*}/', $routeUrl, $routeParams);
        
        if(!(count($routeParams) && $routeParams[0])) {
            return $routeUrl;
        }
        
        // replace route parameter with corresponding values
        foreach ($routeParams as $key => $param) {
            preg_match('/[a-z]+/', $param[0], $matches);

            if(!in_array($matches[0], array_keys($arguments))) {
                throw new RuntimeException('missing route parameter: ' . $matches[0]);
            }

            $routeUrl = str_replace($param, $arguments[$matches[0]], $routeUrl);
        }

        return $routeUrl;
    }

    /**
     * Get the value of baseUrl
     */ 
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the value of baseUrl
     *
     * @return  self
     */ 
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get the value of cacert
     */ 
    public function getCacert()
    {
        return $this->cacert;
    }

    /**
     * Set the value of cacert
     *
     * @return  self
     */ 
    public function setCacert($cacert)
    {
        $this->cacert = $cacert;

        return $this;
    }

}
