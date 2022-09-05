<?php

    /**
     * DataValidator
     * 
     * @abstract
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    abstract class DataValidator
    {
        /**
         * dataDeleted
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function dataDeleted(): bool
        {
            $valid = empty($GLOBALS['_DELETE']) === false;
            return $valid;
        }

        /**
         * dataIncluded
         * 
         * @access  public
         * @static
         * @param   string $param
         * @param   array $data
         * @return  bool
         */
        public static function dataIncluded(string $param, array $data): bool
        {
            $valid = isset($data[$param]) === true;
            return $valid;
        }

        /**
         * dataInputted
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function dataInputted(): bool
        {
            $body = file_get_contents('php://input');
            return $body !== '';
        }

        /**
         * dataPatched
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function dataPatched(): bool
        {
            $valid = empty($GLOBALS['_PATCH']) === false;
            return $valid;
        }

        /**
         * dataPosted
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function dataPosted(): bool
        {
            $valid = empty($_POST) === false;
            return $valid;
        }

        /**
         * validHTTPReferrer
         * 
         * @access  public
         * @static
         * @param   string $host
         * @return  bool
         */
        public static function validHTTPReferrer(string $host): bool
        {
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            if ($referrer === null) {
                return false;
            }
            $referrer = trim($referrer);
            if ($referrer === '') {
                return false;
            }
            $found = stripos($referrer, $host) !== false;
            if ($found === false) {
                return false;
            }
            return true;
        }

        /**
         * validRequestMethod
         * 
         * @access  public
         * @static
         * @param   string $requestMethod
         * @return  bool
         */
        public static function validRequestMethod(string $requestMethod): bool
        {
            $serverRequestMethod = $_SERVER['REQUEST_METHOD'];
            $serverRequestMethod = strtolower($serverRequestMethod);
            $requestMethod = strtolower($requestMethod);
            if ($serverRequestMethod === $requestMethod) {
                return true;
            }
            return false;
        }
    }
