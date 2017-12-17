<?php

    /**
     * SchemaValidator
     * 
     * Manages the validation of a schema against it's defined rules.
     * 
     * @example https://github.com/onassar/PHP-JSON-Validation/tree/master/example
     * @link    https://github.com/onassar/PHP-JSON-Validation
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    class SchemaValidator
    {
        /**
         * _data
         * 
         * @var     array
         * @access  protected
         */
        protected $_data;

        /**
         * _failedRules
         * 
         * @var     array
         * @access  protected
         */
        protected $_failedRules = array();

        /**
         * _storedData
         * 
         * @var     array
         * @access  protected
         */
        protected $_storedData = array();

        /**
         * _libraries
         * 
         * @var     array
         * @access  protected
         */
        protected $_libraries = array(
            'DataValidator.class.php',
            'CurlValidator.class.php',
            'IntegerValidator.class.php',
            'StringValidator.class.php'
        );

        /**
         * _exceptions
         * 
         * @var     array
         * @access  protected
         */
        protected $_exceptions = array(
            'RuleValidationException.class.php',
            'SchemaFormattingException.class.php',
            'SchemaValidationException.class.php'
        );

        /**
         * _schema
         * 
         * @var     Schema
         * @access  protected
         */
        protected $_schema;

        /**
         * _senstitiveToParentBlocking
         * 
         * @note    Also defaults to `false` during instantiation
         * @var     boolean (default: false)
         * @access  protected
         */
        protected $_senstitiveToParentBlocking = false;

        /**
         * __construct
         * 
         * @access  public
         * @param   Schema $schema
         * @param   array $data (default: array())
         * @param   boolean $senstitiveToParentBlocking (default: false)
         *          Determines whether rules should prevent further validation
         *          if/when a rule has failed to validate *and* it's parent has
         *          it's `blocking` attribute set to `true`
         * @return  void
         */
        public function __construct(
            Schema $schema,
            array $data = array(),
            $senstitiveToParentBlocking = false
        ) {
            // local storage
            $this->_schema = $schema;
            $this->_data = $data;
            $this->_senstitiveToParentBlocking = $senstitiveToParentBlocking;

            // pass along special properties
            $this->_data['__get__'] = $_GET;
            $this->_data['__data__'] = &$this->_data;
            $this->_data['__post__'] = $_POST;
            $this->_data['__schema__'] = $schema;
            $this->_data['__schemaValidator__'] = $this;

            // library booting
            foreach ($this->_libraries as $library) {
                require_once $library;
            }

            // exception booting
            foreach ($this->_exceptions as $exception) {
                require_once $exception;
            }
        }

        /**
         * _addFailedRule
         * 
         * Adds a rule object to the <_failedRules> array.
         * 
         * @note    decoupled to allow logging and/or changing what gets
         *          pushed to the <_failedRules> array
         * @access  protected
         * @param   array &$rule
         * @return  void
         */
        protected function _addFailedRule(array &$rule)
        {
            array_push($this->_failedRules, $rule);
        }

        /**
         * _callInterstitial
         * 
         * Makes a call to a "rules" interstiatial. This call doesn't expect
         * any response (eg. true/false), and is meant to run code between
         * checking validation rules.
         * 
         * @access  protected
         * @param   array $rule
         * @return  void
         */
        protected function _callInterstitial(array $rule)
        {
            // parameters passed
            $params = array();
            if (isset($rule['params']) === true) {

                // pass along the rule and parent as magic properties
                $this->_data['__this__'] = $rule;
                if (isset($rule['_parent'])) {
                    $this->_data['__parent__'] = $rule['_parent'];
                }

                // parameter formatting
                foreach ($rule['params'] as &$param) {
                    $param = $this->_templateParam($param);
                }
                $params = $rule['params'];
            }
            call_user_func_array($rule['interstitial'], $params);
        }

        /**
         * _isBlockingRule
         * 
         * Is currently set up such that if a rule has the `blocking` attribute
         * set to `true`, validation will end (through this method returning
         * false).
         * 
         * It also includes a check against the parent rule (if found), whereby
         * if the parent has been set to blocking, validation will also end.
         * This, however, only occurs when the `_senstitiveToParentBlocking`
         * instance-property is set to true. This can be done during the
         * `SchemaValidation` instantiation.
         * 
         * @access  protected
         * @param   array &$rule
         * @return  boolean
         */
        protected function _isBlockingRule(array &$rule)
        {
            if (isset($rule['blocking']) === true) {
                return (boolean) $rule['blocking'];
            }
            if ($this->_senstitiveToParentBlocking === true) {
                if (isset($rule['_parent']) === true) {
                    return $this->_isBlockingRule($rule['_parent']);
                }
            }
            return false;
        }

        /**
         * _checkRule
         * 
         * @access  protected
         * @param   array $rule
         * @return  boolean
         */
        protected function _checkRule(array $rule)
        {
            // parameters passed
            $params = array();
            if (isset($rule['params']) === true) {

                // pass along the rule and parent as magic properties
                $this->_data['__this__'] = $rule;
                if (isset($rule['_parent']) === true) {
                    $this->_data['__parent__'] = $rule['_parent'];
                }

                // parameter formatting
                foreach ($rule['params'] as &$param) {
                    $param = $this->_templateParam($param);
                }
                $params = $rule['params'];
            }

            // evaluate/return rule check
            return call_user_func_array($rule['validator'], $params);
        }

        /**
         * _checkRules
         * 
         * @access  protected
         * @param   array $rules
         * @param   array|null $parent
         * @return  void
         */
        protected function _checkRules(array &$rules, &$parent = null)
        {
            // rule iteration
            foreach ($rules as $count => &$rule) {

                // ignore the rule if it's been disabled
                if (
                    isset($rule['disabled']) === true
                    && $rule['disabled'] === true
                ) {
                    continue;
                }

                // store the parent for rule blocking checks
                if (is_null($parent) === false) {
                    $rule['_parent'] = $parent;
                }

                /**
                 * If it's an "interstitial", and not a "validator", run the
                 * callback directly.
                 * 
                 * Interstitials are not meant to validate any data or return
                 * booleans. Rather, they are meant to setup other pieces of
                 * the validation flow.
                 * 
                 * For example, you may validate that a user id is specified.
                 * You may then define an interstitial after that rule (or as
                 * a subrule), which retrieves the user's username, and sends
                 * them an email, adds it to the available data that the
                 * validator has access to, or anything else that seems
                 * relevant
                 */
                if (isset($rule['interstitial']) === true) {
                    $this->_callInterstitial($rule);
                    if (
                        isset($rule['rules']) === true
                        && empty($rule['rules']) === false
                    ) {
                        if (
                            isset($rule['rules'][0]['interstitial']) === false
                            && isset($rule['rules'][0]['validator']) === false
                        ) {
                            throw new SchemaFormattingException(
                                '`rules` property must be array of ' .
                                'rules. One specifically defined.'
                            );
                        }
                        $this->_checkRules($rule['rules'], $rule);
                    }
                } else {

                    /**
                     * If the rule passed, check it's <rules> array (this
                     * occurs recursively)
                     */
                    if ($this->_checkRule($rule)) {
                        if (
                            isset($rule['rules']) === true
                            && empty($rule['rules']) === false
                        ) {
                            if (
                                isset($rule['rules'][0]['interstitial']) === false
                                && isset($rule['rules'][0]['validator']) === false
                            ) {
                                throw new SchemaFormattingException(
                                    '`rules` property must be array of ' .
                                    'rules. One specifically defined.'
                                );
                            }
                            $this->_checkRules($rule['rules'], $rule);
                        }
                    }
                    // or else, the rule has failed to pass
                    else {

                        /**
                         * If the rule wasn't setup to act as a funnel (a rule
                         * that is marked as a funnel need-not validate
                         * successfully for the schema itself to be considered
                         * valid; rules can be marked as a funnel to allow for
                         * subrules to be validated in a predicatable,
                         * controllable way), mark the rule as having failed.
                         * 
                         * aka. rule didn't pass, and wasn't set as a funnel,
                         * then the rule has failed to validate
                         */
                        if (
                            isset($rule['funnel']) === false
                            || $rule['funnel'] === false
                        ) {
                            $this->_addFailedRule($rule);

                            // alternative calls (aka. failure callbacks)
                            $this->_initiateAlternatives($rule);

                            /**
                             * If this failing-rule was setup as <blocking>
                             * (rules having the property <blocking> marked as
                             * <true> are deemed too important for any further
                             * rules to be tested), error out (ought to be
                             * caught by caller)
                             */
                            if ($this->_isBlockingRule($rule) === true) {
                                throw new RuleValidationException(
                                    'Rule failed'
                                );
                            }
                        } else {

                            // alternative calls (aka. failure callbacks)
                            $this->_initiateAlternatives($rule);
                        }
                    }
                }
            }
        }

        /**
         * _getTraversedValue
         * 
         * Traverses down the _data array, searching for a value matched by the
         * keys of the value exploded via the period delimter (that was a
         * mouthful).
         * For example, if the value passed in is __patch__.data.settings, this
         * method will check if __patch__ is valid reference, and if so, check
         * that for data, and then check data for settings. False is returned if
         * anywhere along the way, the value couldn't be retrieved by traversing
         * down using the period delimiter.
         * 
         * @access  protected
         * @param   string $value
         * @return  false|mixed
         */
        protected function _getTraversedValue($value)
        {
            $reference = $this->_data;
            $keys = explode('.', $value);
            foreach ($keys as $key) {
                if (isset($reference[$key]) === false) {
                    return false;
                }
                $reference = $reference[$key];
            }
            return $reference;
        }

        /**
         * _initiateAlternatives
         * 
         * @access  protected
         * @param   array $rule
         * @return  void
         */
        protected function _initiateAlternatives(array $rule)
        {
            // rules or interstitials to call when parent failed
            if (
                isset($rule['alternatives']) === true
                && empty($rule['alternatives']) === false
            ) {
                if (
                    isset($rule['alternatives'][0]['interstitial']) === false
                    && isset($rule['alternatives'][0]['validator']) === false
                ) {
                    throw new SchemaFormattingException(
                        '`alternatives` property must be array ' .
                        'of rules. One specifically defined.'
                    );
                }
                $this->_checkRules($rule['alternatives'], $rule);
            }
        }

        /**
         * _templateParam
         * 
         * Replaces the value passed in with the corresponding
         * data-source-value, if found. If an array is passed in, recursively
         * does so.
         * 
         * This could be useful with the String `inList` validation check. It
         * also, however, outsources the template replacement from the
         * `checkRule` method, cleaning it and decoupling the logic slightly.
         * 
         * Made the check more lenient, in that it doesn't require the string
         * to be an exact match against a data-source argument.
         * 
         * Current supports cases such as:
         * - Param value of "{name}s"
         * - Templated value becomes "Oliver Nassars"
         * 
         * Does *not* currently support cases such as:
         * - Param value of "{fname} {lname}"
         * 
         * This is for two reasons:
         * 1. The regular expression match is limited to one match
         * 2. The replacement code only replaces the specific key, not the
         *    array of keys with their respective values
         * 
         * @access  protected
         * @param   string|array $param
         * @return  string|array
         */
        protected function _templateParam($param)
        {
            // if the parameter defined in the schema is an array
            if (is_array($param) === true) {

                // recursively template the property
                foreach ($param as &$entry) {
                    $entry = $this->_templateParam($entry);
                }
            }
            // otherwise if it's a string
            elseif (is_string($param) === true) {
                $key = array();

                // Standard variable match
                if (preg_match('/{([a-zA-Z0-9-\._]+)}/', $param, $key)) {

                    // if the parameter exists in the validator's data source
                    if (isset($this->_data[$key[1]]) === true) {

                        /**
                         * If the param *value* that should be sent in for
                         * validation is a string (eg. a _POST'd username or 
                         * email address), run a string replacement to get the
                         * proper value from the _data array.
                         * 
                         * Note that PHP will cast POST'd data as strings, even
                         * if they are entered as numbers/floats, etc.
                         */
                        if (is_string($this->_data[$key[1]]) === true) {
                            $param = str_replace(
                                $key[0],
                                $this->_data[$key[1]],
                                $param
                            );
                        }
                        /**
                         * Otherwise if it's not a string, set the param value
                         * to be the exact mixed value. This allows for the
                         * following example:
                         * 
                         * In a schema, you set a param of {userModel}. Then,
                         * when you set up the SchemaValidator instance, in the
                         * instantiation you pass along a data property such
                         * as:
                         * 
                         * 'userModel' => $userModel
                         * 
                         * This allows for passing non-primitive (eg. strings,
                         * integers, booleans, arrays and floats) types to
                         * validation methods.
                         * 
                         * Additionally, the `__data__` property is added to
                         * the `_data` property, to allow a validation method
                         * to pass around all data posted.
                         * 
                         * See the `DataValidator` class, `dataIncluded` method.
                         */
                        else {
                            $param = $this->_data[$key[1]];
                        }
                    }
                    // Key value could not be found
                    else {
                        // Check if a value can be found by traversal
                        $traversed = $this->_getTraversedValue($key[1]);
                        if ($traversed !== false) {
                            $param = $traversed;
                        }
                        /**
                         * Key value couldn't be found, either directly or by
                         * traversing down period-delimited keys.
                         */
                        else {
                            throw new SchemaFormattingException(
                                'Invalid data-source specified. Entry ' .
                                'name *' . ($param) . '* not found in ' .
                                'data source.'
                            );
                        }
                    }
                }
            }
            /**
             * If the param was a number of boolean, no templating is done, and
             * the param is returned directly
             */
            return $param;
        }

        /**
         * addData
         * 
         * @access  public
         * @param   string $key
         * @param   mixed $data
         * @return  void
         */
        public function addData($key, $data)
        {
            $this->_data[$key] = $data;
        }

        /**
         * getData
         * 
         * @access  public
         * @return  array
         */
        public function getData()
        {
            return $this->_data;
        }

        /**
         * getFailedRules
         * 
         * Returns an array of rules that failed during the validation process.
         * Important to note here is that the response here does *not* preserve
         * the hierarchy defined in the schema.
         * 
         * What that means, is that if a failing rule is a subrule of another
         * rule, the "parent" rule will not be returned. The response from this
         * function is *one* dimensional, respective to rules.
         * 
         * @access  public
         * @param   boolean $includeParents (default: true)
         * @return  array
         */
        public function getFailedRules($includeParents = true)
        {
            $failedRules = $this->_failedRules;
            if ($includeParents === false) {
                foreach ($failedRules as &$rule) {
                    if (isset($rule['_parent']) === true) {
                        unset($rule['_parent']);
                    }
                }
                unset($rule);// See: http://php.net/manual/en/language.references.unset.php
            }
            return $failedRules;
        }

        /**
         * getSchema
         * 
         * @access  public
         * @return  Schema
         */
        public function getSchema()
        {
            return $this->_schema;
        }

        /**
         * getStoredData
         * 
         * @access  public
         * @return  array
         */
        public function getStoredData()
        {
            return $this->_storedData;
        }

        /**
         * store
         * 
         * @access  public
         * @param   string $key
         * @param   mixed $value
         * @return  void
         */
        public function store($key, $value)
        {
            $this->_storedData[$key] = $value;
        }

        /**
         * valid
         * 
         * Returns whether or not the schema has been validated against the
         * data passed in.
         * 
         * Uses exception throwing to break out of the validation loop when a
         * required rule has failed.
         * 
         * @access  public
         * @return  boolean
         */
        public function valid()
        {
            $rules = call_user_func(
                array($this->_schema, $this->_schema->getMethod())
            );
            try {
                $this->_checkRules($rules);
            } catch(Exception $exception) {
                if (get_class($exception) !== 'RuleValidationException') {
                    throw $exception;
                }
            }
            return count($this->_failedRules) === 0;
        }
    }
