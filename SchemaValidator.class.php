<?php

    /**
     * SchemaValidator
     * 
     * @author Oliver Nassar <onassar@gmail.com<
     */
    class SchemaValidator
    {
        /**
         * _data.
         * 
         * @var array
         * @access protected
         */
        protected $_data;

        /**
         * _errors.
         * 
         * @var array
         * @access protected
         */
        protected $_errors = array();

        /**
         * _libraries
         * 
         * @var array
         * @access protected
         */
        protected $_libraries = array(
            'StringValidator.class.php'
        );

        /**
         * _schema.
         * 
         * @var Schema
         * @access protected
         */
        protected $_schema;

        /**
         * __construct function.
         * 
         * @access public
         * @param Schema $schema
         * @param array $data
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
         * _checkRule function.
         * 
         * @access protected
         * @param array $rule
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
                if (preg_match('/^{([a-zA-Z-\._]+)}$/', $param, $key)) {

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
         * _checkRules function.
         * 
         * @access protected
         * @param array $rules
         * @return boolean
         */
        protected function _checkRules(array $rules)
        {
            // rule iteration
            foreach ($rules as $rule) {
                if ($this->_checkRule($rule)) {
                    $this->_checkRules($rule['rules']);
                } else {

                    /**
                     * Mark as error the failed rule wasn't mean to act as a
                     *     funnel.
                     */
                    if (!$rule['funnel']) {
                        array_push($this->_errors, $rule['error']);
                    }
                }
            }
        }

        /**
         * getErrors function.
         * 
         * @access public
         * @return array
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * valid function. Returns whether or not the schema has been validated
         *     against the data passed in.
         * 
         * @access public
         * @return bool
         */
        public function valid()
        {
            $rules = $this->_schema->getServerRules();
            $this->_checkRules($rules);
            return count($this->_errors) === 0;
        }
    }
