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
    }
