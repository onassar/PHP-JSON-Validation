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
         * @param   int $int
         * @param   int $min
         * @return  bool
         */
        public static function greaterThan(int $int, int $min): bool
        {
            return (int) $int > (int) $min;
        }

        /**
         * greaterThanOrEqualTo
         * 
         * @access  public
         * @static
         * @param   int $int
         * @param   int $min
         * @return  bool
         */
        public static function greaterThanOrEqualTo(int $int, int $min): bool
        {
            return (int) $int >= (int) $min;
        }

        /**
         * lessThan
         * 
         * @access  public
         * @static
         * @param   int $int
         * @param   int $max
         * @return  bool
         */
        public static function lessThan(int $int, int $max): bool
        {
            return (int) $int < (int) $max;
        }

        /**
         * lessThanOrEqualTo
         * 
         * @access  public
         * @static
         * @param   int $int
         * @param   int $max
         * @return  bool
         */
        public static function lessThanOrEqualTo(int $int, int $max): bool
        {
            return (int) $int <= (int) $max;
        }
    }
