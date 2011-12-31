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
    }
