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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_infoValueCollectionFactory.php';


class tx_ptgsaconfmgm_abstractArticle extends tx_ptobjectstorage_ptRowObject {
	
	/**
	 * @var tx_ptgsaconfmgm_infoValueCollection
	 */
	protected $infoValueCollection;
	
	public function getRowUid() {
		return $this->rowUid;
	}
	
	public function save() {
		
		if($this->isNewRow) {
			$this->set_tstamp(time());
			$this->set_crdate(time());
			$this->set_cruser_id($GLOBALS['TSFE']->fe_user->user['uid']);
		}
		
		$rowUid = parent::save();
		$this->set_uid($rowUid);
		
		$this->saveArticleCode();
		$this->saveInfoValues();
		
		return $rowUid;
	}
	
	protected function saveArticleCode() {
		if($this->get_articlecode()) return;
		
		$this->set_articlecode($this->generateArticleCode());
		parent::save();
	}
	
	/**
	 * generate an "unique" article code
	 * 
	 * @return int article code
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 26.05.2010
	 */
	protected function generateArticleCode() {
		tx_pttools_assert::isInRange($this->rowUid, 1, 99999);
		
		list($usec, $sec) = explode(' ', microtime());
  		$seed = (float) $sec + ((float) $usec * 100000);
  		srand($seed);
		$rand = rand(1, 9999);
		
		if($rand < 1000) $rand += 1000;
		
		return sprintf("%s%05s", $rand, $this->rowUid);
	}
	
	
	/**
	 * Set the Reference UIDs and save every value
	 * @return void
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	protected function saveInfoValues() {
		if(is_a($this->infoValueCollection, 'tx_ptgsaconfmgm_infoValueCollection')) {
			
			if($this->tableName=='tx_ptconference_domain_model_persdata') {
				$persArticleUid = $this->rowUid;
				$relArticleUid = 0;
			} else {
				$persArticleUid = $this->get_persdata();
				$relArticleUid = $this->rowUid;
			}			
			
			foreach($this->infoValueCollection as $infoValue) {
				$infoValue->set_persdata($persArticleUid);
				$infoValue->set_relarticle($relArticleUid);
				$infoValue->save();
			}
		}
	}
	
	
	/**
	 * fill the infovaluecollection with data
	 * 
	 * @param $infoValueArray
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	protected function setInfoValuesFromArray($infoValueArray) {
		if(!is_array($infoValueArray) || count($infoValueArray) == 0) return;
			
		if(!is_a($this->infoValueCollection, 'tx_ptgsaconfmgm_infoValueCollection')) {
			$this->infoValueCollection = tx_ptgsaconfmgm_infoValueCollectionFactory::createEmptyCollection();	
		}	
		
		foreach($infoValueArray as $infoType => $infoValue) {
			
			if($this->infoValueCollection->hasItem($infoType)) {
				$infoValueObject= $this->infoValueCollection->getItemById($infoType);
			} else {
				$infoValueObject = new tx_ptgsaconfmgm_infoValue();	
			}
		
			$infoValueObject->set_infotype($infoType);
			$infoValueObject->set_infovalue($infoValue);
			
			$this->infoValueCollection->addInfoValue($infoValueObject, $infoType);	
		}		
	}
	
	/**
	 * @return array InfoValues
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	protected function getInfoValuesAsArray() {
		foreach($this->infoValueCollection as $infoValueObject) {
			$infoData[$infoValueObject->get_infotype()] = $infoValueObject->get_infovalue();
		}
		
		return $infoData;
	}
	
}
?>