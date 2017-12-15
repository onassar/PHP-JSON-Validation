<?php

    /**
     * PHP-JSON-Validation example
     *
     * In the spirit of a typical blog post comment (requesting a visitors name,
     * email address, website url, comment, and whether they want to be
     * notified of updates to the post), the following example demonstrates how
     * to create a Schema instance based off a json-file, create a
     * SchemaValidator object based off that Schema object and sample/dummy
     * data, and validate them against one-another.
     *
     * This example validates the following:
     *  - a name is given
     *  - an email is either not provided, or a valid email format
     *  - a website is either not provided, or a valid url format
     *  - a comment is given
     *    - if a comment was given, that it does not exceed 250 characters
     *  - an <updates> input initiates a funnel, which is evaluated as follows:
     *    - if checked (evaluates to the string <true>), requires that the email
     *      provided is a valid format (meaning, it can no longer be empty)
     *    - if not checked, the subrule specified is not evaluated; this does
     *      *not* affect the subsequent validation of the schema, since it is
     *      marked as a funnel
     * 
     * The rule evaluating whether a name was specified contains the <blocking>
     * property, which prevents any further rules from being evaluated upon
     * failure. This can be useful for security (eg. to ensure certain
     * high-level rules are succesfully evaluated before processing sub-rules
     * which may attempt to select, modify or remove from a database).
     *
     * Of note: rules are processed in sequence, regardless of whether their
     * processing is depenedent on a previous rule having been successfully
     * passed, unless the <blocking> or <funnel> properties are used
     * intelligently.
     *
     * @author  Oliver Nassar <onassar@gmail.com>
     */

    // dummy data
    $_POST = array(
        'name' => '',
        'email' => '',
        'website' => 'http://www.olivernassar.com/',
        'comment' => 'Hello World!',
        'updates' => 'true'
    );

    // instantiation (could be <Schema> or <SmartSchema> instance)
    require_once '../Schema.class.php';
    require_once '../SchemaValidator.class.php';
    $schema = (new Schema('comment.json'));
    $validator = (new SchemaValidator($schema, $_POST));

    // validation
    if ($validator->valid()) {
        echo 'Valid';
    } else {
        echo 'Invalid<br />Offending rule(s):' .
             '<pre>';
        $offending = $validator->getFailedRules();
        print_r($offending);
        echo '</pre>';
    }
    exit(0);
