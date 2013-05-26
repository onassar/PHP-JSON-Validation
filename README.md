PHP-JSON-Validation
===
One of the more complicated of my classes/projects, the PHP-JSON-Validation
library focuses on a cross-paradigm (server/client) data validation solution.

Quick Note
===
This library is meant to be used directly with the front-end validation
project [JS-JSON-Validation](https://github.com/onassar/JS-JSON-Validation).

Both libraries are developed to work off the same `schema` object type in order
to have validation of a form (or data source) validated in the same way.

Summary
===
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

**Note** To limit the validator to one *unsuccessful* rule per validation flow,
simply mark each rules `blocking` attribute as `true`. See documentation
below for a detailed overview of the `blocking` property.

Validation Pieces
===
If you&#039;ve looked at the example and want more background on the different
validation components at work within an actual
[JSON schema](https://github.com/onassar/PHP-JSON-Validation/blob/master/example/comment.json),
then I&#039;ll attempt to provide that here.

Each JSON schema is, well, a JSON document whose sole contents is encompassed in
an array. Each array element is defined as a `rule`. Each `rule` is itself
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
was passed in during the `SchemaValidator` instantiation (eg. if trying to
validate a password input that was posted, this string value may resemble
`{password-input-name}`).

There is no limit to the number of parameters that can be defined for a rule,
however the validating class/method must allow the correct number of parameters
and their respect type, otherwise a PHP error will be thrown.

### blocking (optional)
This *optional* attribute/property, if included, ought to contain a boolean
value, which intrinsically infers the rule&#039;s importance.

Blocking rules are meant to act as a catch-all. By default,
`SchemaValidator` instances will evaluate all rules that have been defined.
However if a `blocking` property is defined for a rule, and that rule fails,
all further validation will end.

If a failing rule has a parent rule, and that parent has the `blocking`
attribute set to `true`, the validation process will *also* end.

For example, validating a user&#039;s authorization for an application, or
whether a request came in from a proper source, are good real-world cases of
the `blocking` attribute&#039;s usage.

### rules (optional)
This *optional* attribute/property, if included, ought to be an array whose
signature matches, recursively, the attributes/properties defined in this
document.

A rule&#039;s `rules` array is evaluated only if it&#039;s parent rule is
successfully evaluated. This can be useful in cases whereby a field must exist
for it to have a subsequent rule applied to it.

For example, if a user is choosing a username during an account registration
process, a requirement may be that the username is 4 characters long. The schema
could reflect this structure by defining a `minLength` rule, and within that
rule&#039;s `rules` array, pointing to the validation class and method to
ensure the username isn&#039; taken.

In this example, an unnecessary database hit can be prevented if a username is
not the proper length. Many other examples can be drawn from this concept,
though.

### funnel (optional)
This *optional* attribute/property, if included, ought to be set as a boolean.
It&#039;s binary state determines whether or not the rule it&#039;s applied to
is meant solely to act as a funnel for further rules. The evaluation of the rule
itself does not determine the success of failure of the `SchemaValidation`
instance itself.

For example, during a blog post commenting flow, a user may be prompted to
optionally include their email address to receive further updates. In order to
receive these updates, they must also click a checkbox marking they would like
to receive them.

This produces two situations. One in which an email address is not required, and
one in which it is, if and only if the user checks the checkbox indicating they
would like to receive updates.

In this case, a rule would be created for the checkbox. It would check to see if
the input evaluates to a proper string (for example, &quot;true&quot; or
&quot;on&quot;). This rule would contain a `rules` array itself, containing
the email validation rule for the email input.

Finally, this sub-rule would have a `blocking` attribute/property set to `true`.
The result would be that if the checkbox evaluates to true, *it funnels* the
sub-rules array into consideration. The email validation rule then becomes
relevant and required (due to the `blocking` being set to the boolean `true`).

Otherwise, if the checkbox is not checked, **SchemaValidator** does not go
ahead with the email validation sub-rule, and is also not considered a failure
due to the checkbox not being checked.

### error (suggested)
I listed this property as *suggested*, rather than *optional*, as it is in no
way used by the validating engine. It is rather useful in the error handling
phases of a validation flow.

If included, this property ought to (but is not required) be an object
containing the property that failed (eg. the name, as a string, of the input
that was being tested against) along with an error message for the failure.

While in reality it could be anything (eg. an error code that is then logged; a
url that the user is then redirected to), the above use-case has worked pretty
solid for me. See below for examples of how localization could be used with
errors.

Flexibility
===
The intent behind the structure of this library is that of extensibility.
Besides being able to fully define your own validation classes and methods,
the <funnel> and <blocking> properties are meant to give the ability to derive
complex validation hierarchies.

Additionally, properties and attributes can be added to any schema. Upon a rule
failure, these properties will be passed along in the failed rules array for the
`SchemaValidator` instance. When a rule errors out, it by default stores the
entire rule in the `_failed` array, which can be accessed through the
instance&#039;s `getFailedRules` method.

A practical example of defining custom attributes would be by defining an error
object as follows:

``` json

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

```

This would allow for localization to be contained within the schema, while
keeping it clean and decoupled from the actual validation logic.

SmartSchema
===
The `SmartSchema` class is not required for the validation outlined above.
However it provides very convenient filtering for use with the
[JS-JSON-Validation](https://github.com/onassar/JS-JSON-Validation) library,
which handles client side validation that is based off the same schema file.

There is no difference in instantiation or usage, other than adding a `range`
attribute to the schema definition. This attribute ought to contain an array
which defines whether the rule should be run by the client, server, or both
validation engines.

### Sample SmartSchema Rule

``` json

    {
        "validator": ["StringValidator", "uniqueEmail"],
        "range": ["server"],
        "params": ["{email}"],
        "error": {
            "input": "email",
            "message": "A user with this email is already registered."
        }
    },

```

As can be inferred from above, a `uniqueEmail` property would (but not required
to) be processed and/or validated on the server side. While it could be done
through ajax, it requires access to a database. As a result, the `range` array
attribute contains only one string: `server`. A `SmartSchema` instance is able
to parse the rules according to this range, and present them for validation
appropriately (to either a
[server side](https://github.com/onassar/PHP-JSON-Validation) validation flow or
[client side](https://github.com/onassar/JS-JSON-Validation) validation flow).

Security
===
Depending on your validation requirements, values that have a rule applied
against them may cause security bugs. This is not managed by the library.

For example, if a regular expression is run against a string, with the
expectation that the string has had it&#039;s contents encoded/escaped, this
could cause a serious server-level fault. This should be handled independent
from the validation process. See the
[PHP-Security](https://github.com/onassar/PHP-Security) functions available.
These work exceptionally well for me during the validation process.

Specifying Child Rules as Path to Schema
===
In a standard flow, a rule may have a sub `rules` array, which contains child
rules that should be run if the parent rule validates to true.

However, a relative path (eg. `secure.json`) can be specified in place of this
array. When this is the case, the rules specified in that file will dynamically
be pulled into the current schema for validation.

This process is recursive as well. Thus, you could chain together many
different validation schemas through one original document.

Magic Data
===
The following data is available for usage within schema validation rules:
- `__data__` An array of all the data passed into the `SchemaValidator`
constructor. This is passed by reference, so careful if you make any changes to
it. Since it's passed by reference, it will contain any data added dynamically
through the `SchemaValidator` method `addData`
- `__schema__` A reference to the schema validator itself
- `__schemaValidator__` A reference to the schema validator itself
- `__get__`
- `__post__`
- `__this__`
- `__parent__`

An example of the usefulness of the `__data__` property is to ensure certain
data has been passed into the validator. For example, if you are passing in
`$_POST` data directly to a validator, you may not know if a certain input was
posted. You can now write rules to check that, passing in the param `__data__`
to check against.

The `__schema__` data is a reference to the `Schema` instance that is being
validated. This could be useful to lookup other rules during the validation
logic of a different rule.

The `__schemaValidator__` data is a reference to the `SchemaValidator` instance that
is performing the checks. This can be useful for dynamically adding data to the
*set* of data, which can then be used in the validation process by other rules.


Interstitials
===
Methods can be called which run code between validation checks, which I'm calling interstitials.

These calls do not require a `true` or `false` return value, and are meant to be run between rules, in order to run some "middleware" logic between calls.

An example entry in a schema could look like so:


``` json

{
    "interstitial": ["Interstitials", "logSomethingToFile"],
    "params": ["{userId}", "{__schemaValidator__}"]
}

```

The `logSomethingToFile` method will be called, receiving the defined arguments. This can be useful in cases where you want to log certain events, or, for example, email an administrator of a certain event.

The same magic parameters can be passed in. Additionally, a `rules` array can be specified for child-rules to be run after the interstitial has been called.

Loadable Rule Sets (Update: May 25th, 2013)
===
If you have a group of rules that ought to be processed in multiple schemas, you can specify the *relative*
path to the rule file. The framework will load them in and process them accordingly.

Example:

``` json

{
    "validator": ["DataValidator", "dataPosted"],
    "blocking": true,
    "error": {
        "message": "Please enter your username/password"
    },
    "rules": "loginDetailsValidation.json"
}

```
