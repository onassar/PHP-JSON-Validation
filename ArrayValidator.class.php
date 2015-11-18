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
         * Returns whether all the keys passed in can be found in the array
         * passed in.
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
         * limitedKeys
         * 
         * Returns one of the keys passed in doesn't exist in the array passed
         * in.
         * 
         * @see    http://stackoverflow.com/a/18250308
         * @access public
         * @static
         * @param  array $arr
         * @param  array $keys
         * @return boolean
         */
        public static function limitedKeys(array $arr, array $keys)
        {
            foreach (array_keys($arr) as $key) {
                if (!in_array($key, $keys)) {
                    return false;
                }
            }
            return true;
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
