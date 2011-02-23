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


class tx_ptgsaconfmgm_infoValue extends tx_ptobjectstorage_ptRowObject {
	
	public function __construct($id=0) {
		$this->tableName = 'tx_ptconference_domain_model_persarticleinfo_values';
		parent::__construct($id);
	}
	
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'crdate',
			'cruser_id',
			'infotype',
			'infovalue',
			'persdata',
			'relarticle',
		);
		
	}
	
	public function save() {
		
		if($this->isNewRow) {
			$this->set_tstamp(time());
			$this->set_crdate(time());
			$this->set_cruser_id($GLOBALS['TSFE']->fe_user->user['uid']);
		}
		
		return parent::save();
	}
	
}


?>