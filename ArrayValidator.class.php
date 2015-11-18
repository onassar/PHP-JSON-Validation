<?php

    /**
     * ArrayValidator
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class ArrayValidator
    {
        /**
         * containsKeys
         * 
         * @see    http://stackoverflow.com/a/18250308
         * @access public
         * @static
         * @param  array $arr
         * @param  array $keys
         * @return boolean
         */
        public static function containsKeys(array $arr, array $keys)
        {
            if (count(array_intersect_key(array_flip($keys), $arr)) === count($keys)) {
                return true;
            }
            return false;
        }

        /**
         * notEmpty
         * 
         * @access public
         * @static
         * @param  array $arr
         * @return boolean
         */
        public static function notEmpty(array $arr)
        {
            return !empty($arr);
        }
    }
