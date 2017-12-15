<?php

    /**
     * StringValidator
     * 
     * Provides various string-based validation checks.
     * 
     * @abstract
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    abstract class StringValidator
    {
        /**
         * _decode
         * 
         * @access  protected
         * @static
         * @param   mixed $mixed
         * @return  mixed
         */
        protected static function _decode($mixed)
        {
            if (is_array($mixed) === true) {
                foreach ($mixed as $key => $value) {
                    $mixed[$key] = decode($value);
                }
                return $mixed;
            }
            return html_entity_decode($mixed, ENT_QUOTES, 'UTF-8');
        }

        /**
         * contains
         * 
         * Checks whether a substring is contained in the passed in string
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   string $substr
         * @return  boolean
         */
        public static function contains($str, $substr)
        {
            return strpos($str, $substr) !== false;
        }

        /**
         * doesNotContain
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   string $substr
         * @return  boolean
         */
        public static function doesNotContain($str, $substr)
        {
            return strpos($str, $substr) === false;
        }

        /**
         * email
         * 
         * Checks whether a string is in the proper email format
         * 
         * @note    should work with o.nassar+label@sub.domain.info
         * @access  public
         * @static
         * @param   string $str presumable email address which should be
         *          validated to ensure it is in fact the valid format
         * @return  boolean whether or not the email is valid
         */
        public static function email($str)
        {
            return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
            return preg_match(
                '/^[_a-z0-9-]+([\.|\+][_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i',
                $str
            ) > 0;
        }

        /**
         * emailOrUrl
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
         */
        public static function emailOrUrl($str)
        {
            return self::email($str) || self::url($str);
        }

        /**
         * emptyOrEmail
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
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
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
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
         * @access  public
         * @static
         * @param   string $str
         * @param   string $comparison
         * @return  boolean
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
         * @access  public
         * @static
         * @param   string|integer $str value to search for existence in
         * @param   array $list array of values to use as a basis for an
         *          existence check
         * @return  boolean whether or not $str is in the $list array
         */
        public static function inList($str, array $list)
        {
            return in_array($str, $list, true);
        }

        /**
         * isAlphaNumeric
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   boolean $allowPeriods (default: false)
         * @param   boolean $allowDashes (default: false)
         * @return  boolean
         */
        public static function isAlphaNumeric($str, $allowPeriods = false, $allowDashes = false)
        {
            $pattern = '/^[a-zA-Z0-9';
            $pattern .= $allowPeriods ? '\.' : '';
            $pattern .= $allowDashes ? '\-' : '';
            $pattern .= ']+$/';
            return preg_match($pattern, $str);
        }

        /**
         * isJson
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
         */
        public static function isJson($str)
        {
            return (
                is_string($str) === true
                && (
                    is_object(json_decode($str))
                    || is_array(json_decode($str))
                )
            ) ? true : false;
        }

        /**
         * isMobileNumber
         * 
         * @note    Expects format +15551234
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
         */
        public static function isMobileNumber($str)
        {
            return preg_match('/^\+[0-9]+$/', $str);
        }

        /**
         * maxLength
         * 
         * Checks whether a maximum (inclusive) length of characters has been
         * met in the string passed in.
         * 
         * @note    without the _decode call, characters such as '&' would may
         *          be counted as 5 characters in length (eg. &amp;); since this
         *          would confuse user's, string's are decoded here. Therefore,
         *          keep in mind that a database column should be longer than
         *          what you are allowing from a form-input stage (eg. incase
         *          they enter a string with a large number of multi-byte
         *          characters)
         * @access  public
         * @static
         * @param   string $str string to check for at most $max characters
         * @param   integer $max maximum number of characters required for the
         *          string
         * @return  boolean whether or not the string is a maximum length of
         *          $max characters
         */
        public static function maxLength($str, $max)
        {
            return strlen(self::_decode($str)) <= $max;
        }

        /**
         * matches
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   string $pattern
         * @return  boolean
         */
        public static function matches($str, $pattern)
        {
            return preg_match($pattern, $str) === 1;
        }

        /**
         * minLength
         * 
         * Checks whether a minimum (inclusive) length of characters has been
         * met in the string passed in.
         * 
         * @note    without the _decode call, characters such as '&' would may
         *          be counted as 5 characters in length (eg. &amp;); since this
         *          would confuse user's, string's are decoded here. Therefore,
         *          keep in mind that a database column should be longer than
         *          what you are allowing from a form-input stage (eg. incase
         *          they enter a string with a large number of multi-byte
         *          characters)
         * @access  public
         * @static
         * @param   string $str string to check for at least $min characters
         * @param   integer $min minimum number of characters required for the
         *          string
         * @return  boolean whether or not the string is a minimum length of $min
         *          characters
         */
        public static function minLength($str, $min)
        {
            return strlen(self::_decode($str)) >= $min;
        }

        /**
         * notEmail
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
         */
        public static function notEmail($str)
        {
            return self::email($str) === false;
        }

        /**
         * notEmpty
         * 
         * Checks whether a passed in string is empty
         * 
         * @access  public
         * @static
         * @param   string $str string which should be checked for emptiness
         * @return  boolean whether or not the string is empty
         */
        public static function notEmpty($str)
        {
            return trim($str) !== '';
        }

        /**
         * url
         * 
         * @see     http://snippets.dzone.com/posts/show/3654
         * @access  public
         * @static
         * @param   string $str
         * @return  boolean
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
