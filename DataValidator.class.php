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
            return empty($GLOBALS['_DELETE']) === false;
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
            return isset($data[$param]) === true;
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
            return empty($GLOBALS['_PATCH']) === false;
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
            return empty($_POST) === false;
        }
    }
