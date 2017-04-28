<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
$_EXTKEY = 'mksanitizedparameters';

require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'tca/ext_tables.php');
