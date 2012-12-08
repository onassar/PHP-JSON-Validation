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
         * @param  String $param
         * @param  Array $data
         * @return Boolean
         */
        public static function dataIncluded($param, array $data)
        {
            return isset($data[$param]);
        }
    }
