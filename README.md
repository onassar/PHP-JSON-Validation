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

### params (required)

### error (optional)

### failsafe (optional)

### rules (optional)

### funnel (optional)
This *optional* attribute ought to contain a boolean value of either true or
false. This attribute determines whether or now 
