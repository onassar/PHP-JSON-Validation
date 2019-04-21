<?php

    // parent class loading
    require_once 'Schema.class.php';

    /**
     * SmartSchema
     * 
     * Schema extension that provides inclusion/exclusion features (for robust
     * filtering, and for use with the <JS-JSON-Validation> library).
     * 
     * This child class could be extended to have filtering occur on a different
     * property (by default, the <range> attribute, which ought to be an array
     * of string values).
     * 
     * Additionally, if a schema is particularily complicated, a caching library
     * could be introduced (based on a schema file's modified timestamp) to
     * speed up the recursive parsing/filtering of client and server rules.
     * 
     * @final
     * @extends Schema
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class SmartSchema extends Schema
    {
        /**
         * _method
         * 
         * The method that ought to be called for rules retrieval.
         * 
         * @var     string
         * @access  protected
         */
        protected $_method = 'getServerRules';

        /**
         * __limit
         * 
         * Recursively excludes rules which do *not* contain the <inclusion>
         * value in the <range> array/attribute of a schema rule.
         * 
         * @access  private
         * @param   array $rules
         * @param   string $inclusion
         * @return  array
         */
        private function __limit(array $rules, string $inclusion): array
        {
            // loop through supplied rules
            foreach ($rules as $x => $info) {

                /**
                 * If a range was specified, but it doesn't contain the
                 * <inclusion> parameter
                 */
                if (
                    isset($info['range']) === true &&
                    in_array($inclusion, $info['range']) === false
                ) {

                    // remove the rule from the set of rules for this filter
                    unset($rules[$x]);
                }
                // otherwise, if the sub-rules array/attribute isn't empty
                elseif (empty($info['rules']) === false) {

                    // recurisvely attempt to limit the rules for this sub-array
                    $rules[$x]['rules'] = $this->__limit(
                        $info['rules'],
                        $inclusion
                    );
                }
            }

            // return rules that have been recursively-cleared
            return array_values($rules);
        }

        /**
         * __construct
         * 
         * Defaults rules that ought to be validated to be server side.
         * 
         * @access  public
         * @param   string $path
         * @param   bool $allowPHPInSchemas (default: false)
         * @return  void
         */
        public function __construct(string $path, bool $allowPHPInSchemas = false)
        {
            parent::__construct($path, $allowPHPInSchemas);
        }

        /**
         * getClientRules
         * 
         * Retrieves rules from parent class that are assigned to be run on the
         * client-side (based on the <range> attribute).
         * 
         * @access  public
         * @return  array
         */
        public function getClientRules(): array
        {
            // return the recursively-limited schema
            $rules = self::getRules();
            return $this->__limit($rules, 'client');
        }

        /**
         * getServerRules
         * 
         * Retrieves rules from parent class that are assigned to be run on the
         * server-side (based on the <range> attribute).
         * 
         * @access  public
         * @return  array
         */
        public function getServerRules(): array
        {
            // return the recursively-limited schema
            $rules = self::getRules();
            return $this->__limit($rules, 'server');
        }
    }
