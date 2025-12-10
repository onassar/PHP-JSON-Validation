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
         * between
         * 
         * @access  public
         * @static
         * @param   int $int
         * @param   int $min
         * @param   int $max
         * @return  bool
         */
        public static function between(int $int, int $min, int $max): bool
        {
            if ($int < $min) {
                return false;
            }
            if ($int > $max) {
                return false;
            }
            return true;
        }

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
            $valid = (int) $int > (int) $min;
            return $valid;
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
            $valid = (int) $int >= (int) $min;
            return $valid;
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
            $valid = (int) $int < (int) $max;
            return $valid;
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
            $valid = (int) $int <= (int) $max;
            return $valid;
        }
    }
