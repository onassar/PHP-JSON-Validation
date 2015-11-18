<?php

    /**
     * DataValidator
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class DataValidator
    {
        /**
         * dataDeleted
         * 
         * @access public
         * @static
         * @return boolean
         */
        public static function dataDeleted()
        {
            return !empty($GLOBALS['_DELETE']);
        }

        /**
         * dataIncluded
         * 
         * @access public
         * @static
         * @param  string $param
         * @param  array $data
         * @return boolean
         */
        public static function dataIncluded($param, array $data)
        {
            return isset($data[$param]);
        }

        /**
         * dataInputted
         * 
         * @access public
         * @static
         * @return boolean
         */
        public static function dataInputted()
        {
            $body = file_get_contents('php://input');
            return $body !== '';
        }

        /**
         * dataPatched
         * 
         * @access public
         * @static
         * @return boolean
         */
        public static function dataPatched()
        {
            return !empty($GLOBALS['_PATCH']);
        }

        /**
         * dataPosted
         * 
         * @access public
         * @static
         * @return boolean
         */
        public static function dataPosted()
        {
            return !empty($_POST);
        }
    }
