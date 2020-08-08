<?php

    /**
     * SchemaValidationException
     * 
     * @extends Exception
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    class SchemaValidationException extends Exception
    {
        /**
         * __construct
         * 
         * @access  public
         * @param   string $message
         * @param   int $code (default: 0)
         * @param   Exception|null $previous (default: null)
         * @return  void
         */
        public function __construct(string $message, int $code = 0, ?Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
