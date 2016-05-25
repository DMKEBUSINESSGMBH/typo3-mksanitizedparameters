Rules
=====

The rules define how parameters will be sanitized.

Register rules
--------------

The rules config should be saved serialized for performance reasons. (as for realurl)

The rules can be included/registered for example in the ext\_localconf.php like this:

~~~~ {.sourceCode .php}
// $defaultRules = array(
//   tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY   => array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES)
// );
$defaultRulesForFrontend = unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:521;}}');
tx_mksanitizedparameters_Rules::addRulesForFrontend($defaultRulesForFrontend);
// $rulesForBackend =  array(
//   tx_mksanitizedparameters_Rules::DEFAULT_RULES_KEY   => array(FILTER_SANITIZE_STRING, FILTER_SANITIZE_MAGIC_QUOTES)
// );
$rulesForBackend = unserialize('a:1:{s:7:"default";a:2:{i:0;i:513;i:1;i:521;}}');
tx_mksanitizedparameters_Rules::addRulesForBackend($rulesForBackend);
~~~~

Note that you can register rules for frontend and backend.

[DefineRules](DefineRules.md)

[ExistingRules](ExistingRules.md)