Define rules
============

Order of rules
--------------

The order of executing the rules is as follows:

-   special rules
-   common rules (\_\_common): will be used if there is no special rule for a parameter
-   default rules (\_\_default): will be used if there is neither a special rule nor a common rule

Include filter
--------------

Filter can be defined as follows: (this is also the order in which filter configuration is looked up. first will serve)

```php
array(
   \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
   //OR
   \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => array(
      'filter' => array(
         FILTER_SANITIZE_FULL_SPECIAL_CHARS,
         FILTER_SANITIZE_ADD_SLASHES,
      ),
      'flags' => FILTER_FLAG_ENCODE_AMP,
   ),
   //OR
   \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => array(
      FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      FILTER_SANITIZE_ADD_SLASHES,
   )
);
```

Filter with own class
---------------------

```php
array(
   'subArray' => array(
      //that's the way to call a custom filter!
      //make sure to have your custom class autoloaded.
      'someValue' => array(
         'filter' => FILTER_CALLBACK,
         'options' => array(
            \DMK\MkSanitizedParameters\Sanitizer\AlphaSanitizer::class,
            'sanitizeValue',
         ),
      ),
   ),
);
```

Special rules
-------------

```php
array(
   'myParameterQualifier' => array(
      'uid' => FILTER_SANITIZE_NUMBER_INT,
      'searchWord' => array(
         'filter' => array(
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_SANITIZE_ADD_SLASHES   ,
         ),
         'flags' => FILTER_FLAG_ENCODE_AMP,
      ),
      'subArray' => array(
         //so all unconfigured parameters inside subArray will get
         //the following default sanitization
         \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
      ),
   ),
);
```

Common rules
------------

Will be inherited in lower levels. You can also overwrite common rules in lower levels. This means the lower the rule is, the higher is it's priority.

```php
array(
   \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
      // no matter at which position every parameter with the name someOtherValueToo
      // will be sanitized with the following configuration as long as there is no
      // special configuration
      'someOtherValueToo' => array(
          FILTER_SANITIZE_FULL_SPECIAL_CHARS,
          FILTER_SANITIZE_ADD_SLASHES,
      ),
   ),
   'myExt' => array(
      // this will overwrite the common rules for someOtherValueToo inside myExt.   
      // the common config for otherValueToo is availabe, too
      \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
         'someOtherValueToo' => array(
             FILTER_SANITIZE_FULL_SPECIAL_CHARS,
             FILTER_SANITIZE_ADD_SLASHES,
         ),
      ),
   ),
   \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
      'otherValueToo' => array(
          FILTER_SANITIZE_FULL_SPECIAL_CHARS,
          FILTER_SANITIZE_ADD_SLASHES,
      ),
   ),
);
```

Hint for inheritance of common rules
------------------------------------

Filter configs with a array which contains the key "filter" have higher priority than filter configs with a array which contains several filter as array. The highest priority have single filter configs. see orderOfRules

So if you have the following config:

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => array(
          FILTER_SANITIZE_NUMBER_INT,
       ),
    ),
);
```

Which is overwritten in a lower level:

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => array(
          'filter' => array(
             FILTER_SANITIZE_FULL_SPECIAL_CHARS,
             FILTER_SANITIZE_ADD_SLASHES, 
          ),
          'flags'  => FILTER_FLAG_ENCODE_AMP,
       ),
    ),
);
```

And again is overwritten in an even lower level:

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => array(
          FILTER_SANITIZE_NUMBER_INT,
       ),
    ),
);
```

Than the following filter config is used in the lowest level:

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => array(
          'filter' => array(
             FILTER_SANITIZE_FULL_SPECIAL_CHARS,
             FILTER_SANITIZE_ADD_SLASHES,
          ),
          'flags' => FILTER_FLAG_ENCODE_AMP,
       ),
    ),
);
```

The filter config for the lowest level should be therefore as follows:

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => FILTER_SANITIZE_NUMBER_INT,
    ),
);
```

or

```php
array(
    \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
       'commonValue' => array(
          'filter' => array(
             FILTER_SANITIZE_NUMBER_INT,
          )
       )
    ),
);
```

Default rules
-------------

Will be inherited to lower levels if they don't have a config.

```php
array(
   'myExt' => array(
      // this will overwrite the default rules for everything inside myExt
      'default' => array(
         FILTER_SANITIZE_FULL_SPECIAL_CHARS,
         FILTER_SANITIZE_ADD_SLASHES,
      ),
   ),
   \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS , 
);
```

Example
-------

Parameter array:

```php
array(
   'myParameterQualifier' => array(
      'uid' => 1,
      'searchWord' => 'johndoe',
      'subArray' => array(
         'someOtherValue' => '...',
         'someOtherValueToo' => '...',
      ),
      'someOtherValueToo' => '...',
   ),
   'nextParameterQualifier' => '...',
);
```

Config:

```php
array(
   'myParameterQualifier' => array(
      'uid' => FILTER_SANITIZE_NUMBER_INT,
      'searchWord' => array(
         'filter' => array(
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_SANITIZE_ADD_SLASHES,
         ),
         'flags' => FILTER_FLAG_ENCODE_AMP,
      ),
      'subArray' => array(
         //so all unconfigured parameters inside subArray will get
         //the following default sanitization
         \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_NUMBER_INT,
      )
   ),
   \DMK\MkSanitizedParameters\Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
   \DMK\MkSanitizedParameters\Rules::COMMON_RULES_KEY => array(
      // no matter at which position every parameter with the name someOtherValueToo
      // will be sanitized with the following configuration as long as there is no
      // special configuration
      'someOtherValueToo' => array(
          FILTER_SANITIZE_FULL_SPECIAL_CHARS,
          FILTER_SANITIZE_ADD_SLASHES,
      ),  
   ),
);
```

Those rules would be used for the parameter array:

```php
array(
   'myParameterQualifier' => array(
      'uid' => 'the special rule will be used',
      'searchWord' => 'the special rule will be used',
      'subArray' => array(
         'someOtherValue' => 'the default rule of subarray will be used',
         'someOtherValueToo' => 'the common rule will be used',
      ),
      'someOtherValueToo' => 'the common rule will be used',
   ),
   'nextParameterQualifier' => 'the default rule of the root will be used',
);
```
