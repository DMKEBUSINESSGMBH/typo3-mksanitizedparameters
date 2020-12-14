Rules
=====

The rules define how parameters will be sanitized.

Register rules
--------------

The rules config should be saved serialized for performance reasons. (as for realurl)

The rules can be included/registered for example in the ext\_localconf.php like this:

```php
\DMK\MkSanitizedParameters\Rules::addRulesForFrontend(
    [
        \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => [
            FILTER_SANITIZE_STRING, 
            FILTER_SANITIZE_ADD_SLASHES
        ]
    ]
);
\DMK\MkSanitizedParameters\Rules::addRulesForBackend(
    [
        \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => [
            FILTER_SANITIZE_STRING, 
            FILTER_SANITIZE_ADD_SLASHES
        ]
    ]
);
```

Note that you can register rules for frontend and backend.

[DefineRules](DefineRules.md)

[ExistingRules](ExistingRules.md)
