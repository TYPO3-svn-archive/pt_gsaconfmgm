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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_infoValue.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_infoValueCollection.php';


require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_persArticleInfoAccessor.php';



class tx_ptgsaconfmgm_infoValueCollectionFactory {
	
	
	/**
	 * @return tx_ptgsaconfmgm_infoValueCollection
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public static function createEmptyCollection() {
		return new tx_ptgsaconfmgm_infoValueCollection();
	}
	
	/**
	 * @param $persDataUid
	 * @return tx_ptgsaconfmgm_infoValueCollection
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public static function createFromDataBaseByPersDataUid($persDataUid) {
		$infoValues = tx_ptgsaconfmgm_persArticleInfoAccessor::getInstance()->getInfoValues($persDataUid,0);
		return self::createFromInfoValueArray($infoValues);
	}
	
	/**
	 * @param $relarticleUid
	 * @return tx_ptgsaconfmgm_infoValueCollection
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public static function createFromDataBaseByRelArticleUid($persDataUid, $relarticleUid) {
		$infoValues = tx_ptgsaconfmgm_persArticleInfoAccessor::getInstance()->getInfoValues($persDataUid,$relarticleUid);
		return self::createFromInfoValueArray($infoValues);
	}
	
	/**	
	 * @param $infoValueArray
	 * @return tx_ptgsaconfmgm_infoValueCollection
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public static function createFromInfoValueArray($infoValueArray) {
	
		$collection = new tx_ptgsaconfmgm_infoValueCollection();
		foreach($infoValueArray as $key => $value) {
			$valueObject = new tx_ptgsaconfmgm_infoValue($key);
			
			$collection->addInfoValue($valueObject, $value['infotype']);
		}
		
		return $collection;
	}
	
}

?>