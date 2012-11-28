<?php

    /**
     * StringValidator
     * 
     * Provides various string-based validation checks.
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class StringValidator
    {
        /**
         * _decode
         * 
         * @access protected
         * @static
         * @param  mixed $mixed
         * @return mixed
         */
        protected static function _decode($mixed)
        {
            if (is_array($mixed)) {
                foreach ($mixed as $key => $value) {
                    $mixed[$key] = decode($value);
                }
                return $mixed;
            }
            return html_entity_decode($mixed, ENT_QUOTES, 'UTF-8');
        }

        /**
         * email
         * 
         * Checks whether a string is in the proper email format
         * 
         * @notes  should work with o.nassar+label@sub.domain.info
         * @access public
         * @static
         * @param  string $str presumable email address which should be validated
         *         to ensure it is in fact the valid format
         * @return boolean whether or not the email is valid
         */
        public static function email($str)
        {
            return preg_match(
                '/^[_a-z0-9-]+([\.|\+][_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i',
                $str
            ) > 0;
        }

        /**
         * emailOrUrl
         * 
         * @access public
         * @static
         * @param  string $str
         * @return boolean
         */
        public static function emailOrUrl($str)
        {
            return self::email($str) || self::url($str);
        }

        /**
         * emptyOrEmail
         * 
         * @access public
         * @static
         * @param  string $str
         * @return boolean
         */
        public static function emptyOrEmail($str)
        {
            if (self::notEmpty($str) === false) {
                return true;
            }
            return self::email($str);
        }

        /**
         * emptyOrUrl
         * 
         * @access public
         * @static
         * @param  string $str
         * @return boolean
         */
        public static function emptyOrUrl($str)
        {
            if (self::notEmpty($str) === false) {
                return true;
            }
            return self::url($str);
        }

        /**
         * equals
         * 
         * @access public
         * @static
         * @param  string $str
         * @param  string $comparison
         * @return boolean
         */
        public static function equals($str, $comparison)
        {
            return $str === $comparison;
        }

        /**
         * inList
         * 
         * Checks whether a passed in value (string|int) exists in a list.
         * 
         * @access public
         * @static
         * @param  string|int $str value to search for existence in
         * @param  array $list array of values to use as a basis for an existence
         *         check
         * @return boolean whether or not $str is in the $list array
         */
        public static function inList($str, array $list)
        {
            return in_array($str, $list, true);
        }

        /**
         * maxLength
         * 
         * Checks whether a maximum (inclusive) length of characters has been
         * met in the string passed in.
         * 
         * @notes  without the _decode call, characters such as '&' would may be
         *         counted as 5 characters in length (eg. &amp;); since this
         *         would confuse user's, string's are decoded here. Therefore,
         *         keep in mind that a database column should be longer than
         *         what you are allowing from a form-input stage (eg. incase
         *         they enter a string with a large number of multi-byte
         *         characters)
         * @access public
         * @static
         * @param  string $str string to check for at most $max characters
         * @param  integer $max maximum number of characters required for the
         *         string
         * @return boolean whether or not the string is a maximum length of $max
         *         characters
         */
        public static function maxLength($str, $max)
        {
            return strlen(self::_decode($str)) <= $max;
        }

        /**
         * minLength
         * 
         * Checks whether a minimum (inclusive) length of characters has been
         * met in the string passed in.
         * 
         * @notes  without the _decode call, characters such as '&' would may be
         *         counted as 5 characters in length (eg. &amp;); since this
         *         would confuse user's, string's are decoded here. Therefore,
         *         keep in mind that a database column should be longer than
         *         what you are allowing from a form-input stage (eg. incase
         *         they enter a string with a large number of multi-byte
         *         characters)
         * @access public
         * @static
         * @param  string $str string to check for at least $min characters
         * @param  integer $min minimum number of characters required for the
         *         string
         * @return boolean whether or not the string is a minimum length of $min
         *         characters
         */
        public static function minLength($str, $min)
        {
            return strlen(self::_decode($str)) >= $min;
        }

        /**
         * notEmpty
         * 
         * Checks whether a passed in string is empty
         * 
         * @access public
         * @static
         * @param  string $str string which should be checked for emptiness
         * @return boolean whether or not the string is empty
         */
        public static function notEmpty($str)
        {
            return $str !== '';
        }

        /**
         * url
         * 
         * @see    http://snippets.dzone.com/posts/show/3654
         * @access public
         * @static
         * @param  string $str
         * @return boolean
         */
        public static function url($str)
        {
            return preg_match(
                '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}' .
                '(([0-9]{1,5})?\/.*)?$/ix',
                $str
            ) > 0;
        }
    }
