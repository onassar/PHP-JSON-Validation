<?php

    /**
     * SchemaValidator
     * 
     * Manages the validation of a schema against it's defined rules.
     * 
     * @author  Oliver Nassar <onassar@gmail.com>
     * @example https://github.com/onassar/PHP-JSON-Validation/tree/master/example
     */
    class SchemaValidator
    {
        /**
         * _data
         * 
         * @var    array
         * @access protected
         */
        protected $_data;

        /**
         * _failed
         * 
         * @var    array
         * @access protected
         */
        protected $_failed = array();

        /**
         * _libraries
         * 
         * @var    array
         * @access protected
         */
        protected $_libraries = array(
            'DataValidator.class.php',
            'StringValidator.class.php'
        );

        /**
         * _schema
         * 
         * @var    Schema
         * @access protected
         */
        protected $_schema;

        /**
         * _addFailedRule function. Adds a rule object to the <_failed> array.
         * 
         * @notes  decoupled to allow logging and/or changing what gets
         *         pushed to the <_failed> array
         * @access protected
         * @param  array $rule
         * @return void
         */
        protected function _addFailedRule(array $rule)
        {
            array_push($this->_failed, $rule);
        }

        /**
         * __construct
         * 
         * @access public
         * @param  Schema $schema
         * @param  Array $data (default: array())
         * @return void
         */
        public function __construct(Schema $schema, array $data = array())
        {
            // local storage
            $this->_schema = $schema;
            $this->_data = $data;

            // pass along special properties
            $this->_data['__data__'] = $this->_data;

            // library booting
            foreach ($this->_libraries as $library) {
                require_once $library;
            }
        }

        /**
         * _templateParam
         * 
         * Replaces the value passed in with the corresponding
         * data-source-value, if found. If an array is passed in, recursively
         * does so.
         * 
         * This could be useful with the String `inList` validation check. It
         * also, however, outsources the template replacement from the
         * `checkRule` method, cleaning it and decoupling the logic slightly.
         * 
         * Made the check more lenient, in that it doesn't require the string
         * to be an exact match against a data-source argument.
         * 
         * Current supports cases such as:
         * - Param value of "{name}s"
         * - Templated value becomes "olivers"
         * 
         * Does *not* currently support cases such as:
         * - Param value of "{fname} {lname}"
         * 
         * This is for two reasons:
         * 1. The regular expression match is limited to one match
         * 2. The replacement code only replaces the specific key, not the
         *    array of keys with their respective values
         * 
         * @access protected
         * @param  String|Array $param
         * @return String|Array
         */
        protected function _templateParam($param)
        {
            // if the parameter defined in the schema is an array
            if (is_array($param)) {

                // recursively template the property
                foreach ($param as &$entry) {
                    $entry = $this->_templateParam($entry);
                }
            }
            // otherwise if it's a string
            elseif (is_string($param)) {
                $key = array();
                if (preg_match('/{([a-zA-Z0-9-\._]+)}/', $param, $key)) {

                    // if the parameter exists in the validator's data source
                    if (isset($this->_data[$key[1]])) {

                        /**
                         * If the param *value* that should be sent in for
                         * validation is a string (eg. a _POST'd username or 
                         * email address), run a string replacement to get the
                         * proper value from the _data array.
                         * 
                         * Note that PHP will cast POST'd data as strings, even
                         * if they are entered as numbers/floats, etc.
                         */
                        if (is_string($this->_data[$key[1]])) {
                            $param = str_replace(
                                $key[0],
                                $this->_data[$key[1]],
                                $param
                            );
                        }
                        /**
                         * Otherwise if it's not a string, set the param value
                         * to be the exact mixed value. This allows for the
                         * following example:
                         * 
                         * In a schema, you set a param of {userModel}. Then,
                         * when you set up the SchemaValidator instance, in the
                         * instantiation you pass along a data property such
                         * as:
                         * 
                         * 'userModel' => $userModel
                         * 
                         * This allows for passing non-primitive (eg. strings,
                         * integers, booleans, arrays and floats) types to
                         * validation methods.
                         * 
                         * Additionally, the `__data__` property is added to
                         * the `_data` property, to allow a validation method
                         * to pass around all data posted.
                         * 
                         * See the `DataValidator` class, `dataIncluded`
                         * method.
                         */
                        else {
                            $param = $this->_data[$key[1]];
                        }
                    } else {
                        throw new Exception(
                            'Invalid data-source specified. Entry ' .
                            'name *' . ($param) . '* not found in ' .
                            'data source.'
                        );
                    }
                }
            }
            /**
             * If the param was a number of boolean, no templating is done, and
             * the param is returned directly
             */
            return $param;
        }

        /**
         * _checkRule
         * 
         * @access protected
         * @param  array $rule
         * @return boolean
         */
        protected function _checkRule(array $rule)
        {
            // parameters passed
            $params = array();
            if (isset($rule['params'])) {

                // parameter formatting
                $params = &$rule['params'];
                foreach ($params as &$param) {
                    $param = $this->_templateParam($param);
                }
            }

            // evaluate/return rule check
            return call_user_func_array($rule['validator'], $params);
        }

        /**
         * _checkRules
         * 
         * @access protected
         * @param  array $rules
         * @return void
         */
        protected function _checkRules(array $rules)
        {
            // blocking triggered boolean
            $blocked = false;

            // rule iteration
            foreach ($rules as $rule) {

                // if a blocking rule has failed
                if ($blocked === true) {
                    break;
                }

                /**
                 * If the rule passed, check it's <rules> array (this occurs
                 * recursively)
                 */
                if ($this->_checkRule($rule)) {
                    if (isset($rule['rules'])) {
                        $this->_checkRules($rule['rules']);
                    }
                } else {

                    /**
                     * If the rule wasn't setup to act as a funnel (a rule that
                     * is marked as a funnel need-not validate successfully for
                     * the schema itself to be considered valid; rules can be
                     * marked as a funnel to allow for subrules to be
                     * validated in a predicatable, controllable way), mark the
                     * rule as having failed.
                     * 
                     * aka. rule didn't pass, and wasn't set as a funnel, then
                     * the rule has failed to validate
                     */
                    if (!isset($rule['funnel']) || $rule['funnel'] === false) {
                        $this->_addFailedRule($rule);
                    }

                    /**
                     * If this failing-rule was setup as <blocking> (rules
                     * having the property <blocking> marked as <true> are
                     * deemed too important for any further rules [in this
                     * recursion] to be tested), mark a boolean to prevent
                     * further rule validation within this recursive iteration.
                     */
                    if (isset($rule['blocking']) && $rule['blocking'] === true) {
                        $blocked = true;
                    }
                }
            }
        }

        /**
         * getFailedRules
         * 
         * Returns an array of rules that failed during the validation process.
         * Important to note here is that the response here does *not* preserve
         * the hierarchy defined in the schema.
         * 
         * What that means, is that if a failing rule is a subrule of another
         * rule, the "parent" rule will not be returned. The response from this
         * function is *one* dimensional, respective to rules.
         * 
         * @access public
         * @return array
         */
        public function getFailedRules()
        {
            return $this->_failed;
        }

        /**
         * valid
         * 
         * Returns whether or not the schema has been validated against the data
         * passed in.
         * 
         * @access public
         * @return boolean
         */
        public function valid()
        {
            $rules = call_user_func(
                array($this->_schema, $this->_schema->getMethod())
            );
            $this->_checkRules($rules);
            return count($this->_failed) === 0;
        }
    }
