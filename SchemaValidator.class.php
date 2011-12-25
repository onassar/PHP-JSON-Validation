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
         * _errors.
         * 
         * @var    array
         * @access protected
         */
        protected $_errors = array();

        /**
         * _libraries
         * 
         * @var    array
         * @access protected
         */
        protected $_libraries = array(
            'StringValidator.class.php'
        );

        /**
         * _schema.
         * 
         * @var    Schema
         * @access protected
         */
        protected $_schema;

        /**
         * _addError function. Adds a rule object to the <_errors> array.
         * 
         * @note   decoupled to allow error logging and/or changing what gets
         *         pushed to the <_errors> array
         * @access protected
         * @param  array $rule
         * @return void
         */
        protected function _addError(array $rule)
        {
            array_push($this->_errors, $rule);
        }

        /**
         * __construct
         * 
         * @access public
         * @param  Schema $schema
         * @param  array $data
         * @return void
         */
        public function __construct(Schema $schema, array $data)
        {
            // local storage
            $this->_schema = $schema;
            $this->_data = $data;

            // library booting
            foreach ($this->_libraries as $library) {
                require_once $library;
            }
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
            // parameter formatting
            $params = &$rule['params'];
            foreach ($params as $x => $param) {

                /**
                 * If parameter value ought to be dynamically pulled from
                 * validator data source (based on pattern of parameter)
                 */
                $key = array();
                if (preg_match('/^{([a-zA-Z0-9-\._]+)}$/', $param, $key)) {

                    // if the parameter exists in the validator's data source
                    if (isset($this->_data[$key[1]])) {
                        $params[$x] = $this->_data[$key[1]];
                    } else {
                        $params[$x] = null;
                    }
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
            // failsafe triggered boolean
            $failsafed = false;

            // rule iteration
            foreach ($rules as $rule) {

                // if a failsafe was triggered (a failsafe rule having failed)
                if ($failsafed === true) {
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
                     * rule as having error'd out.
                     * 
                     * aka. rule didn't pass, and wasn't set as a funnel, then
                     * the schema has failed to validate
                     */
                    if (!isset($rule['funnel']) || $rule['funnel'] === false) {
                        $this->_addError($rule);
                    }

                    /**
                     * If this failing-rule was setup as a failsafe (rules
                     * having the property <failsafe> marked as <true> are
                     * deemed too important for any further rules (in this
                     * recursion) to be evaluated)
                     */
                    if (isset($rule['failsafe']) && $rule['failsafe'] === true) {
                        $failsafed = true;
                    }
                }
            }
        }

        /**
         * getErrors
         * 
         * @access public
         * @return array
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * valid
         * 
         * Returns whether or not the schema has been validated against the data
         * passed in.
         * 
         * @access public
         * @return bool
         */
        public function valid()
        {
            $rules = $this->_schema->getRules();
            $this->_checkRules($rules);
            return count($this->_errors) === 0;
        }
    }
