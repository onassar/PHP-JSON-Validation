<?php

    /**
     * IntegerValidator
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class IntegerValidator
    {
        /**
         * greaterThan
         * 
         * @access public
         * @static
         * @param  integer $int
         * @param  integer $minimum
         * @return boolean
         */
        public static function greaterThan($int, $minimum)
        {
            return (int) $int > (int) $minimum;
        }
    }
