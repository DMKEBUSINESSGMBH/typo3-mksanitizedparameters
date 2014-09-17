<?php
/**
 *
 *  Copyright notice
 *
 *  (c) 2012 DMK E-Business GmbH <dev@dmk-ebusiness.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * include required classes
 */
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_mksanitizedparameters');
tx_rnbase::load('tx_mksanitizedparameters_hooks_PreprocessTypo3Requests');

/**
 * damit wir eigentliche klasse ersetzen können
 *
 * @author Hannes Bochmann
 *
 */
class tx_mksanitizedparameters_tests_hooks_PreprocessTypo3Requests
	extends tx_mksanitizedparameters_hooks_PreprocessTypo3Requests {

	/**
	 * wird in ux_tx_mksanitizedparameters_hooks_PreprocessTypo3Requests
	 * überschrieben damit debug mode abgeschaltet werden kann
	 *
	 * @return ux_tx_mksanitizedparameters
	 */
	protected function getMksanitizedparametersMainClass () {
		return ux_tx_mksanitizedparameters;
	}
}

/**
 * wir wollen für die Hook test keinen debug mode
 *
 * @author Hannes Bochmann
 */
class ux_tx_mksanitizedparameters extends tx_mksanitizedparameters {

	/**
	 * @return boolean
	 */
	protected static function getDebugMode() {
		return false;
	}
}