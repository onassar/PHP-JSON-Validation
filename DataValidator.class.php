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
