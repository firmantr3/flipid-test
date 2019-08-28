<?php 

if(!function_exists('dd')) {
    /**
     * Die and dump
     *
     * @param mixed $value
     * @param integer $mode
     * @return void
     */
    function dd($value, $mode = 0) {
        ob_clean();
        header_remove();

        if($mode) {
            var_dump($value);
        }
        else {
            print_r($value);
        }

        exit;
    }
}

if(!function_exists('fromCamelCaseToSnakeCase')) {
    /**
     * convert camel case string to snake case string
     *
     * @param string $input
     * @return string
     */
    function fromCamelCaseToSnakeCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
