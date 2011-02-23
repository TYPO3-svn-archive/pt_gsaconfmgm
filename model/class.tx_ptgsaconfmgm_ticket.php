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

require_once t3lib_extMgm::extPath('pt_objectstorage').'res/abstract/class.tx_ptobjectstorage_ptRowObject.php';
require_once t3lib_extMgm::extPath('pt_objectstorage').'res/objects/class.tx_ptobjectstorage_genericRowObject.php';

require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryAccessor.php';  


class tx_ptgsaconfmgm_ticket extends tx_ptobjectstorage_ptRowObject {
	
	protected $ticketFound = false;
	protected $ticketArticleCategory = '';
	
	/**
	 * initialize the ticket object by ticket-article-code
	 * 
	 * @param $ticketCode
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function __construct($ticketCode) {
		
		$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsaconfmgm.'); 
		
		$this->tableName = 'tx_ptgsaconfmgm_user_article_rel';
		$this->ticketArticleCategory = $tsConf['ticketCategory'];
		
		$this->ticketFound = true;
		
		try {
			parent::__construct(array('article_code' => $ticketCode));	
		} catch (exception $e) {
			$this->ticketFound = false;
		}
	}
	
	
	
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'article_artnr',
			'checked_in',
			'goodies_received',
		);
		
	}
	
	/**
	 * Check if the loaded Data is a valid ticket
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function isValidTicket() {
		// if no ticket data is aviable return false
		if(!$this->ticketFound) return 'checkInError_TicketNotFound';
		
		$artID = $this->getArticleUidbyArtNo($this->get_article_artnr());
		$cat_art_array = tx_ptgsacategories_categoryAccessor::getInstance()->selectCategoriesConnectionsForArticle($artID);
		
		// if the articleCode belongs not to an article wich is in the ticket category return false
		//if(!in_array($this->ticketArticleCategory, $cat_art_array)) return 'checkInError_codeIsNoTicket';
		
		// check if this articlecode is already checked in
		if($this->get_checked_in() == 1) return 'checkInError_TicketAlreadyCheckedIn';

		return true;
	}
	
	/**
	 * TODO: add the article uid to the  tx_ptgsaconfmgm_user_article_rel at order time
	 * 		then use the uid here directly!!
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	protected  function getArticleUidbyArtNo($artNo) {
		$artObject = new tx_ptobjectstorage_genericRowObject('tx_ptgsaminidb_ARTIKEL',
												  tx_ptobjectstorage_genericRowObject::DB_SCHEMA_CONFIG,
												  array('ARTNR' => $artNo),
												  true);
												  
		return $artObject->get_NUMMER();
	}
}
?>