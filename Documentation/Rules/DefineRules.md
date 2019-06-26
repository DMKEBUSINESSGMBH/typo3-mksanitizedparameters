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

~~~~ {.sourceCode .php}
array(
   '__default' => FILTER_SANITIZE_STRING
   //OR
   '__default' => array(
      'filter' => array(
         FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
      ),
      'flags'  => FILTER_FLAG_ENCODE_AMP
   )
   //OR
   '__default' => array(
      FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
   )
)
~~~~

Filter with own class
---------------------

~~~~ {.sourceCode .php}
array(
   'subArray' => array(
      //that's the way to call a custom filter!
      //make sure to have your custom class autoloaded.
      'someValue' => array(
         'filter'    => FILTER_CALLBACK,
         'options'   => array(
            'tx_mksanitizedparameters_sanitizer_Alpha','sanitizeValue'
         )
      )
   )
)
~~~~

Special rules
-------------

~~~~ {.sourceCode .php}
array(
   'myParameterQualifier' => array(
      'uid' => FILTER_SANITIZE_NUMBER_INT
      'searchWord' => array(
         'filter' => array(
            FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
         ),
         'flags'  => FILTER_FLAG_ENCODE_AMP
      ),
      'subArray' => array(
         //so all unconfigured parameters inside subArray will get
         //the following default sanitization
         '__default'    => FILTER_SANITIZE_NUMBER_INT
      )
   )
)    
~~~~

Common rules
------------

Will be inherited in lower levels. You can also overwrite common rules in lower levels. This means the lower the rule is, the higher is it's priority.

~~~~ {.sourceCode .php}
array(
   '__common' => array(
      // no matter at which position every parameter with the name someOtherValueToo
      // will be sanitized with the following configuration as long as there is no
      // special configuration
      'someOtherValueToo' => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)  
   ),
   'myExt' => array(
      // this will overwrite the common rules for someOtherValueToo inside myExt.   
      // the common config for otherValueToo is availabe, too
      '__common' => array(
         'someOtherValueToo' => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)  
      ),
   ),
   '__common' => array(
      'otherValueToo' => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)   
   ),
) 
~~~~

Hint for inheritance of common rules
------------------------------------

Filter configs with a array which contains the key "filter" have higher priority than filter configs with a array which contains several filter as array. The highest priority have single filter configs. see orderOfRules

So if you have the following config:

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => array(
      FILTER_SANITIZE_NUMBER_INT,...
   )
)
~~~~

Which is overwritten in a lower level:

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => array(
      'filter' => array(
         FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
      ),
      'flags'  => FILTER_FLAG_ENCODE_AMP
   )
)
~~~~

And again is overwritten in an even lower level:

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => array(
      FILTER_SANITIZE_NUMBER_INT,...
   )
)
~~~~

Than the following filter config is used in the lowest level:

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => array(
      'filter' => array(
         FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
      ),
      'flags'  => FILTER_FLAG_ENCODE_AMP
   )
)
~~~~

The filter config for the lowest level should be therefore as follows:

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => FILTER_SANITIZE_NUMBER_INT
)
~~~~

or

~~~~ {.sourceCode .php}
'__common' => array(
   'commonValue' => array(
      'filter' => array(
         FILTER_SANITIZE_NUMBER_INT,...
      )
   )
)
~~~~

Default rules
-------------

Will be inherited to lower levels if they don't have a config.

~~~~ {.sourceCode .php}
array(
   'myExt => array(
      // this will overwrite the default rules for everything inside myExt
      default' => array(
         FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
      ),
   ),
   '__default' => FILTER_SANITIZE_STRING  
)
~~~~

Example
-------

Parameter array:

~~~~ {.sourceCode .php}
array(
   'myParameterQualifier' => array(
      'uid' => 1
      'searchWord' => 'johndoe',
      'subArray' => array(
         'someOtherValue' => ...
         'someOtherValueToo' => ...
      ),
      'someOtherValueToo' => ...
   ),
   'nextParameterQualifier' => ...
)
~~~~

Config:

~~~~ {.sourceCode .php}
array(
   'myParameterQualifier' => array(
      'uid' => FILTER_SANITIZE_NUMBER_INT
      'searchWord' => array(
         'filter' => array(
            FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES   
         ),
         'flags'  => FILTER_FLAG_ENCODE_AMP
      ),
      'subArray' => array(
         //so all unconfigured parameters inside subArray will get
         //the following default sanitization
         '__default'    => FILTER_SANITIZE_NUMBER_INT
      )
   ),
   tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY => FILTER_SANITIZE_STRING,
   tx_mksanitizedparameters_Rules::COMMON_RULES_KEY => array(
      // no matter at which position every parameter with the name someOtherValueToo
      // will be sanitized with the following configuration as long as there is no
      // special configuration
      'someOtherValueToo' => array(FILTER_SANITIZE_STRING,FILTER_SANITIZE_MAGIC_QUOTES)  
   ),
)
~~~~

Those rules would be used for the parameter array:

~~~~ {.sourceCode .php}
array(
   'myParameterQualifier' => array(
      'uid' => the special rule will be used
      'searchWord' => the special rule will be used,
      'subArray' => array(
         'someOtherValue' => the default rule of subarray will be used
         'someOtherValueToo' => the common rule will be used
      ),
      'someOtherValueToo' => the common rule will be used
   ),
   'nextParameterQualifier' => the default rule of the root will be used
)
~~~~
