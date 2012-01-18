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
            return json_decode($raw, true);
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
