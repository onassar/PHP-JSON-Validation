<?php

    /**
     * ArrayValidator
     * 
     * @abstract
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    abstract class ArrayValidator
    {
        /**
         * containsKeys
         * 
         * Returns whether all the keys passed in can be found in the array
         * passed in.
         * 
         * @see     http://stackoverflow.com/a/18250308
         * @access  public
         * @static
         * @param   array $arr
         * @param   array $keys
         * @return  bool
         */
        public static function containsKeys(array $arr, array $keys): bool
        {
            if (count(array_intersect_key(array_flip($keys), $arr)) === count($keys)) {
                return true;
            }
            return false;
        }

        /**
         * doesNotContainKey
         * 
         * @access  public
         * @static
         * @param   array $arr
         * @param   mixed $key
         * @return  bool
         */
        public static function doesNotContainKey(array $arr, $key): bool
        {
            if (isset($arr[$key]) === false) {
                return true;
            }
            return false;
        }

        /**
         * limitedKeys
         * 
         * Returns whether one of the keys passed in doesn't exist in the array
         * passed in.
         * 
         * @see     http://stackoverflow.com/a/18250308
         * @access  public
         * @static
         * @param   array $arr
         * @param   array $keys
         * @return  bool
         */
        public static function limitedKeys(array $arr, array $keys): bool
        {
            foreach (array_keys($arr) as $key) {
                if (in_array($key, $keys) === false) {
                    return false;
                }
            }
            return true;
        }

        /**
         * maxNumberOfValues
         * 
         * @access  public
         * @static
         * @param   array $arr
         * @param   int $max
         * @return  bool
         */
        public static function maxNumberOfValues(array $arr, int $max): bool
        {
            return count($arr) <= $max;
        }

        /**
         * notEmpty
         * 
         * @access  public
         * @static
         * @param   array $arr
         * @return  bool
         */
        public static function notEmpty(array $arr): bool
        {
            return empty($arr) === false;
        }

        /**
         * valuesPass
         * 
         * @access  public
         * @static
         * @param   array $arr
         * @param   callable $callback
         * @return  bool
         */
        public static function valuesPass(array $arr, callable $callback): bool
        {
            foreach ($arr as $key => $value) {
                $response = call_user_func_array($callback, array($value));
                if ($response === false) {
                    return false;
                }
            }
            return true;
        }
    }
