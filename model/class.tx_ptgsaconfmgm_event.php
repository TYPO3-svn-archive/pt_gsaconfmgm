<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert (t3extensions@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_objectstorage').'res/abstract/class.tx_ptobjectstorage_ptRowObject.php';
require_once t3lib_extMgm::extPath('pt_objectstorage').'res/objects/class.tx_ptobjectstorage_genericRowObject.php';

/**
 *
 * @package        TYPO3
 * @subpackage     pt_gsaconfmgm
 * @author         Daniel Lienert <lienert@punkt.de>
 * @since          12.05.2010
 */
class tx_ptgsaconfmgm_event extends tx_ptobjectstorage_ptRowObject {
	  
	public function __construct($uid = 0) {
		$this->tableName = 'tx_ptconference_domain_model_event';
		parent::__construct($uid);
	}
		
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'title',
			'description',
			'venue',
			'startdate',
			'enddate',
		);
	}
	
	public function isNew() {
		return $this->isNew;
	}
}
?>