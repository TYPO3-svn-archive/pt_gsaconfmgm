<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Daniel Lienert (t3extensions@punkt.de)
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
***************************************************************/

require_once t3lib_extMgm::extPath('pt_gsauserreg').'pi2/class.tx_ptgsauserreg_pi2.php';

/**
 * 
 * $Id:$
 *
 * @package     TYPO3
 * @subpackage  
 * @author   	Daniel Lienert <lienert@punkt.de>
 * @since       14.08.2009
 */
class tx_ptgsaconfmgm_hooks_ptgsauserreg_pi2 {
	
	/**
	 * manipulate the formdesc
	 * 
	 * @param $obj tx_ptgsauserreg_pi2 object
	 * @param $formDesc FormDescription
	 * @return FormDescription
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 14.08.2009
	 */
	public function main_formdescHook(tx_ptgsauserreg_pi2 $obj, $formDesc) {
		return $formDesc;
	}
}
?>