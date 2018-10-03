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
         * beginsWith
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   string $prefix
         * @return  bool
         */
        public static function beginsWith(string $str, string $prefix): bool
        {
            $pattern = '/^' . ($prefix) . '/';
            if (preg_match($pattern, $str) === 1) {
                return true;
            }
            return false;
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
         * @return  bool
         */
        public static function contains(string $str, string $substr): bool
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
         * @return  bool
         */
        public static function doesNotContain(string $str, string $substr): bool
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
         * @return  bool whether or not the email is valid
         */
        public static function email(string $str): bool
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
         * @return  bool
         */
        public static function emailOrUrl(string $str): bool
        {
            return self::email($str) || self::url($str);
        }

        /**
         * emptyOrEmail
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  bool
         */
        public static function emptyOrEmail(string $str): bool
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
         * @return  bool
         */
        public static function emptyOrUrl(string $str): bool
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
         * @return  bool
         */
        public static function equals(string $str, string $comparison): bool
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
         * @param   string $str value to search for existence in
         * @param   array $list array of values to use as a basis for an
         *          existence check
         * @return  bool whether or not $str is in the $list array
         */
        public static function inList(string $str, array $list): bool
        {
            return in_array($str, $list, true);
        }

        /**
         * isAlphaNumeric
         * 
         * @access  public
         * @static
         * @param   string $str
         * @param   bool $allowPeriods (default: false)
         * @param   bool $allowDashes (default: false)
         * @return  bool
         */
        public static function isAlphaNumeric(string $str, bool $allowPeriods = false, bool $allowDashes = false): bool
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
         * @return  bool
         */
        public static function isJson(string $str): bool
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
         * @return  bool
         */
        public static function isMobileNumber(string $str): bool
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
         * @param   int $max maximum number of characters required for the
         *          string
         * @return  bool whether or not the string is a maximum length of
         *          $max characters
         */
        public static function maxLength(string $str, int $max): bool
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
         * @return  bool
         */
        public static function matches(string $str, string $pattern): bool
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
         * @param   int $min minimum number of characters required for the
         *          string
         * @return  bool whether or not the string is a minimum length of $min
         *          characters
         */
        public static function minLength(string $str, int $min): bool
        {
            return strlen(self::_decode($str)) >= $min;
        }

        /**
         * notEmail
         * 
         * @access  public
         * @static
         * @param   string $str
         * @return  bool
         */
        public static function notEmail(string $str): bool
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
         * @return  bool whether or not the string is empty
         */
        public static function notEmpty(string $str): bool
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
         * @return  bool
         */
        public static function url(string $str): bool
        {
            return preg_match(
                '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}' .
                '(([0-9]{1,5})?\/.*)?$/ix',
                $str
            ) > 0;
        }
    }
