<?php

    /**
     * Schema
     * 
     * General object that encapsulates a JSON-converted validation schema for
     * processing.
     * 
     * @todo    load schema upon instantiation, and validate rules (required
     *          properties/attributes)
     * @author  Oliver Nassar <onassar@gmail.com>
     * @example https://github.com/onassar/PHP-JSON-Validation/tree/master/example
     */
    class Schema
    {
        /**
         * _path
         * 
         * Path to the schema json file
         * 
         * @var    string
         * @access protected
         */
        protected $_path;

        /**
         * _method
         * 
         * The method that ought to be called for rules retrieval (changing of
         * this property is currently only used/useful by the <SmartSchema>
         * class).
         * 
         * @var    string
         * @access protected
         */
        protected $_method = 'getRules';

        /**
         * __construct
         * 
         * @access public
         * @param  string $path
         * @return void
         */
        public function __construct($path)
        {
            $this->_path = $path;
        }

        /**
         * _loadDynamicRules
         * 
         * @access public
         * @param  Array $rules
         * @return Array
         */
        protected function _loadDynamicRules($rules)
        {
            /**
             * Check for a rules property as a string, treat it as though it's
             * a path to another schema, and retrieve it's rules.
             * 
             * This allows for the DRY practice to be applied to the validation
             * schemas.
             */
            foreach ($rules as &$rule) {
                if (isset($rule['rules'])) {
                    if (is_string($rule['rules'])) {
                        $directoryPath = dirname($this->_path);
                        $raw = file_get_contents(
                            ($directoryPath) . '/' . ($rule['rules'])
                        );
                        $decoded = json_decode($raw, true);
                        $rule['rules'] = $this->_loadDynamicRules($decoded);
                    } else {
                        $rule['rules'] = $this->_loadDynamicRules($rule['rules']);
                    }
                }
            }
            return $rules;
        }

        /**
         * getMethod
         * 
         * @access public
         * @return string
         */
        public function getMethod()
        {
            return $this->_method;
        }

        /**
         * getRules
         * 
         * @access public
         * @return array
         */
        public function getRules()
        {
            // grab and return schema contents
            $raw = file_get_contents($this->_path);
            $decoded = json_decode($raw, true);

            // json is formatted invalidly; otherwise return the decoded schema
            if ($decoded === null) {
                throw new Exception('Invalidly formatted json');
            }

            // modify potential sub-rules; return rules (aka. schema)
            $decoded = $this->_loadDynamicRules($decoded);
            return $decoded;
        }

        /**
         * setMethod
         * 
         * @access public
         * @param  String $method
         * @return void
         */
        public function setMethod($method)
        {
            $this->_method = $method;
        }
    }
