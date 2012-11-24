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
            // parameters passed
            $params = array();
            if (isset($rule['params'])) {

                // parameter formatting
                $params = &$rule['params'];
                foreach ($params as $x => $param) {
    
					if(!is_array($param)){
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
