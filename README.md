PHP-JSON-Validation
===
One of the more complicated of my classes/projects, the PHP-JSON-Validation
library focuses on a cross-paradigm (server/client) data validation solution.

In short, I wanted to define how a request (generally, a form being posted)
ought to be validated. This is the server side component which obeys the rules
I&#039;ve come up with. I borrowed many elements that I saw in the wild, and
evolved them in a way that I believe brings about organization in a deceptively
complicated topic.

By working within two different contexts (a Schema and a Validator), and
defining one set of rules (a json file which defines how data ought to be
validated), I believe this approach is organized and extensible.

The easiest way to understand this attempt would be to view the
[example](https://github.com/onassar/PHP-JSON-Validation/tree/master/example)
directory, which outlines a real-world use case.  
The [process.php](https://github.com/onassar/PHP-JSON-Validation/blob/master/example/process.php)
file offers a comprehensive outline of how the logic works within a real-world
example.

Validation Pieces
===

If you&#039;ve looked at the example and want more background on the different
validation components at work within an actual
[JSON schema](https://github.com/onassar/PHP-JSON-Validation/blob/master/example/comment.json),
then I&#039;ll attempt to provide that here.

Each JSON schema is, well, a JSON document whose sole contents is encompassed in
an array. Each array element is defined as a **rule**. Each **rule** is itself
an object, containing the following properties/attributes:

### validator (required)
This *required* property must be formatted as an array, and contain two strings.
In PHP land, it&#039;s values must correspond to a valid callback. The first
string ought to be the name of the class of the rule validation, and the second
the name of the method that returns true or false for this rule.

### params (optional)
The *optional* params property, if included, ought to be structured as an array
containing either a literal value (eg. a number, float, string, boolean, array
or object), or a &quot;templated&quot; value which refers to a data field that
was passed in during the **SchemaValidator** instantiation (eg. if trying to
validate a password input that was posted, this string value may resemble
&quot;{password-input}&quot;).

There is no limit to the number of parameters that can be defined for a rule,
however the validating class/method must allow the correct number of parameters
and their respect type, otherwise a PHP error will be thrown.

### error (optional)
This *optional* property ought to, but is not required, be an object containing
the property that failed (eg. an email input, a password input) along with an
error message for the failure.

While in reality it could be anything (eg. an error code that is then logged; a
url that the user is then redirected to), the above use-case has worked pretty
solid for me. See below for examples of how localization could be used with
errors.

### failsafe (optional)

### rules (optional)

### funnel (optional)
This *optional* attribute ought to contain a boolean value of either true or
false. This attribute determines whether or now 

Flexibility
===
The intent behind the structure of this library is that of extensibility.
Besides being able to full define your own validation classes and methods,
the funnel and failsafe properties are meant to give the ability to derive
complex validation hierarchies.

Additionally, properties and attributes can be added to any schema. Upon a rule
failure, these properties will be passed along in the **errors** array for the
**SchemaValidator** instance. When a rule errors out, it by default stores the
entire rule in the **errors** array, which can be accessed through the
**SchemaValidator**&#039;s **getErrors** method.

A practical example of defining custom attributes would be by defining an error
object as follows:

    {
        "validator": ["StringValidator", "emptyOrEmail"],
        "params": ["{email}"],
        "error": {
            "input": "email",
            "message": {
                "english": "Please enter a valid email address.",
                "french": "..",
                "german": ".."
            }
        }
    },

This would allow for localization to be contained within the schema, while
keeping it clean and decoupled from the actual validation logic.
