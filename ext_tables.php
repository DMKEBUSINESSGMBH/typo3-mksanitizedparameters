<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$_EXTKEY = 'mksanitizedparameters';

require_once(t3lib_extMgm::extPath($_EXTKEY).'tca/ext_tables.php');
