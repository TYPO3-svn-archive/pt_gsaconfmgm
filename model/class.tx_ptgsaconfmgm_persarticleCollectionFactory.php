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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_persArticleAccessor.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticleCollection.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticle.php';


class tx_ptgsaconfmgm_persarticleCollectionFactory {
	
	private static $instance; 
	
	public static function createEmptyCollection() {
		return new tx_ptgsaconfmgm_persarticleCollection();
	}
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new tx_ptgsaconfmgm_persarticleCollectionFactory;
		}
		return self::$instance;
	}
	
	/**
	 * Build a collection from database
	 * @param $eventId int 
	 * @param $customerId int
	 * @return tx_ptgsaconfmgm_persarticleCollection
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	public static function createCollectionFormDatabase($eventId, $customerId) {
		$persArticleDataArray = tx_ptgsaconfmgm_persArticleAccessor::getInstance()->selectAllByEvent($eventId, $customerId);
		$persArticleCollection = new tx_ptgsaconfmgm_persarticleCollection();
		
		foreach($persArticleDataArray as $dataID => $persArticleData) {
			$persArticle = new tx_ptgsaconfmgm_persarticle();
			$persArticle->setPropertiesFromArray($persArticleData);
			$persArticleCollection->addPersArticle($persArticle);
		}
		
		return $persArticleCollection;
	}
	
}
?>