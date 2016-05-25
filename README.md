mksanitizedparameters
=======

What does it do?
----------------

Sanitizes all parameters in $\_GET, $\_POST and $\_COOKIE in the frontend and backend. Every possible parameter can be configured separately. The configuration can be for a specific position in the parameter array or common for every possible position or even default for all parameters, which are not configured.

This way possible attacks like MySQL injections can be prevented even for parameters where attack potential was not suspected. So unclosed security holes are harder or even not at all exploited.

Taking care of the correct data type of a parameter is now done in one single place. You don't need to call intval() for numeric parameters every time you use them (e.g. in a MySQL query). You can use them safe and directly without any further action at any place you want. You just have to provide the correct rule/configuration to be sure your code is not vulnerable to MySQL injections etc.

The sanitizing itself is done through the filter function of PHP. So you can take full advantage of it's features and provide even custom filters.

Features
--------

The extension has 3 modes which can be configured through extension configuration:

-   stealth mode: simulate the sanitizing and log all theoretical actions. you also need to set a page id where the logs are written to.
-   log mode: every parameter which is sanitized (has changed) will be logged at warn level. This way you can investigate what happened. Either it was an attack attempt or the rules have to be adjusted.
-   debug mode: useful during development. every parameter which is sanitized (has changed) will be reported on the screen through a debug message.

Rules
-----

Own rules for sanitizing a parameter can be registered easily. see rules

[UsersManual](Documentation/UsersManual/Index.md)

[Rules](Documentation/Rules/Index.md)

[ChangeLog](Documentation/ChangeLog/Index.md)