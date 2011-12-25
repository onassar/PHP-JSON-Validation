<?php

    /**
     * PHP-JSON-Validation example
     *
     * In the spirit of a typical blog post comment (requesting a visitors name,
     * email address, website url, and comment), the following example
     * demonstrates how to create a Schema instance based off a json-file
     * schema, create a SchemaValidator object based off that Schema object and
     * sample/dummy data, and validate them against one-another.
     *
     * @author Oliver Nassar <onassar@gmail.com>
     */

    // dummy data
    $_POST = array(
        'name' => 'Oliver Nassar',
        'email' => 'onassar@gmail.com',
        'website' => 'not a valid url',
        'comment' => 'Hello World!'
    );

    // instantiation
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
        $offending = $validator->getErrors();
        print_r($offending);
        echo '</pre>';
    }
    exit(0);
