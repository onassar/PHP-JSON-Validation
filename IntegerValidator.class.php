<?php

    /**
     * IntegerValidator
     * 
     * @abstract
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    abstract class IntegerValidator
    {
        /**
         * greaterThan
         * 
         * @access  public
         * @static
         * @param   integer $int
         * @param   integer $min
         * @return  boolean
         */
        public static function greaterThan($int, $min)
        {
            return (int) $int > (int) $min;
        }

        /**
         * greaterThanOrEqualTo
         * 
         * @access  public
         * @static
         * @param   integer $int
         * @param   integer $min
         * @return  boolean
         */
        public static function greaterThanOrEqualTo($int, $min)
        {
            return (int) $int >= (int) $min;
        }

        /**
         * lessThan
         * 
         * @access  public
         * @static
         * @param   integer $int
         * @param   integer $max
         * @return  boolean
         */
        public static function lessThan($int, $max)
        {
            return (int) $int < (int) $max;
        }

        /**
         * lessThanOrEqualTo
         * 
         * @access  public
         * @static
         * @param   integer $int
         * @param   integer $max
         * @return  boolean
         */
        public static function lessThanOrEqualTo($int, $max)
        {
            return (int) $int <= (int) $max;
        }
    }
