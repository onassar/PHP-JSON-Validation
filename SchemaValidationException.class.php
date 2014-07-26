<?php

    /**
     * SchemaValidationException
     * 
     * @extends Exception
     */
    class SchemaValidationException extends Exception
    {
        /**
         * __construct
         *
         * @access public
         * @param  string $message
         * @param  integer $code (default: 0)
         * @param  Exception|null $previous (default: null)
         * @return void
         */
        public function __construct(
            $message,
            $code = 0,
            Exception $previous = null
        ) {
            parent::__construct($message, $code, $previous);
        }
    }
