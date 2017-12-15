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
         * @return  boolean
         */
        public static function dataDeleted()
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
         * @return  boolean
         */
        public static function dataIncluded($param, array $data)
        {
            return isset($data[$param]) === true;
        }

        /**
         * dataInputted
         * 
         * @access  public
         * @static
         * @return  boolean
         */
        public static function dataInputted()
        {
            $body = file_get_contents('php://input');
            return $body !== '';
        }

        /**
         * dataPatched
         * 
         * @access  public
         * @static
         * @return  boolean
         */
        public static function dataPatched()
        {
            return empty($GLOBALS['_PATCH']) === false;
        }

        /**
         * dataPosted
         * 
         * @access  public
         * @static
         * @return  boolean
         */
        public static function dataPosted()
        {
            return empty($_POST) === false;
        }
    }
