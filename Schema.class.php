<?php

    /**
     * Schema
     * 
     * @author Oliver Nassar <onassar@gmail.com<
     */
    class Schema
    {
        /**
         * _path. Path to the schema json file
         * 
         * @var string
         * @access protected
         */
        protected $_path;

        /**
         * __construct function.
         * 
         * @access public
         * @param string $path
         * @return void
         */
        public function __construct($path)
        {
            $this->_path = $path;
        }

        /**
         * getRules function.
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
