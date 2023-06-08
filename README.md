MK Sanitized Parameters
=====================

![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-10.4%20%7C%2011.5-orange?maxAge=3600&style=flat-square&logo=typo3)
[![Latest Stable Version](https://img.shields.io/packagist/v/dmk/mksanitizedparameters.svg?maxAge=3600&style=flat-square&logo=composer)](https://packagist.org/packages/dmk/mksanitizedparameters)
[![Total Downloads](https://img.shields.io/packagist/dt/dmk/mksanitizedparameters.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mksanitizedparameters)
[![Build Status](https://img.shields.io/github/workflow/status/DMKEBUSINESSGMBH/typo3-mksanitizedparameters/PHP-CI.svg?maxAge=3600&style=flat-square&logo=github-actions)](https://github.com/DMKEBUSINESSGMBH/typo3-mksanitizedparameters/actions?query=workflow%3APHP-CI)
[![License](https://img.shields.io/packagist/l/dmk/mksanitizedparameters.svg?maxAge=3600&style=flat-square&logo=gnu)](https://packagist.org/packages/dmk/mksanitizedparameters)



What does it do?
----------------

Sanitizes all parameters in `$_GET`, `$_POST` and `ServerRequestInterface $request`for frontend and backend. 
Every possible parameter can be configured separately. 
The configuration can be for a specific position in the parameter array 
or common for every possible position or even default for all parameters, which are not configured.

This way possible attacks like MySQL injections can be prevented 
even for parameters where attack potential was not suspected. 
So unclosed security holes are harder or even not at all exploited.

Taking care of the correct data type of a parameter is now done in one single place. 
You don't need to call intval() for numeric parameters every time you use them (e.g. in a MySQL query). 
You can use them safe and directly without any further action at any place you want. 
You just have to provide the correct rule/configuration to be sure your code is not vulnerable to MySQL injections etc.

The sanitizing itself is done through the filter function of PHP. 
So you can take full advantage of it's features and provide even custom filters.


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

