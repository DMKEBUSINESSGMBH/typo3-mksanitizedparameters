<?php
defined('TYPO3_MODE') || die('Access denied.');

// $rulesForFrontend = array(
// 	'_pk_ref_*' => FILTER_UNSAFE_RAW,
// 	'_pk_id_*' => FILTER_UNSAFE_RAW,
// 	'_pk_ses_*' => FILTER_UNSAFE_RAW,
// );

$rulesForFrontend = unserialize(
	'a:3:{s:9:"_pk_ref_*";i:516;s:8:"_pk_id_*";i:516;s:9:"_pk_ses_*";i:516;}'
);

tx_mksanitizedparameters_Rules::addRulesForFrontend($rulesForFrontend);