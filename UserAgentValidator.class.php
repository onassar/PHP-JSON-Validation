<?php

    /**
     * UserAgentValidator
     * 
     * @abstract
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    abstract class UserAgentValidator
    {
        /**
         * notEmpty
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function notEmpty(): bool
        {
el(pr($GLOBALS, true));
            $httpUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            if ($httpUserAgent === null) {
                return false;
            }
            return true;
        }
    }
