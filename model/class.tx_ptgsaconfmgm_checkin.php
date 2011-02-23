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

require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_feUsersessionStorageAdapter.php'; // general static library class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general static library class

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_ticket.php'; 


/**
 * 
 * 
 * $Id:$
 *
 * @package      	TYPO3
 * @subpackage  	pt_gsaconfmgm
 * @author         	Daniel Lienert <lienert@punkt.de>
 * @since           01.09.2009
 */
class tx_ptgsaconfmgm_checkin {
	
	/**
	 * array of registered ticket codes
	 */
	protected $ticketCodes = array();	
	protected $operationMode = 'insert';
	
	protected $lastTicketInsertMsg = false;
	
	/**
	 * 
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function __construct() {
		$this->readCodesFromSession();
		
		// get opration mode from session 
		if(trim(tx_pttools_feUsersessionStorageAdapter::getInstance()->read('tx_ptgsaconfmgm_checkin_operationMode'))) {
			$this->operationMode = tx_pttools_feUsersessionStorageAdapter::getInstance()->read('tx_ptgsaconfmgm_checkin_operationMode');
			tx_pttools_assert::isInArray($this->operationMode, array('INSERT', 'STORNO'));
		}
		
		// set a standard value for where TODO: do this in a filter or in list is or ....
		if(!tx_pttools_feUsersessionStorageAdapter::getInstance()->read('tx_ptgsaconfmgm_checkin_ticketListWhere')) {
			tx_pttools_feUsersessionStorageAdapter::getInstance()->store('tx_ptgsaconfmgm_checkin_ticketListWhere', 0);
			$GLOBALS['TSFE']->fe_user->uc['tx_ptgsaconfmgm_checkin_ticketListWhere'] = 0;
		}
	}
	
	/**
	 * add or remove the given ticketcode depend mode
	 * 
	 * @param $ticketCode
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function addRemoveTicketCode($ticketCode) {
		if(strtoupper($this->operationMode) == 'INSERT') {
			$this->addTicketCode($ticketCode);
		} else {
			$this->removeTicketCode($ticketCode);
		}
	}
	
	/**
	 * check if the given ticketcode is already booked 
	 * if not add it to the session   
	 * 
	 * @param $ticketCode
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 01.09.2009
	 */
	public function addTicketCode($ticketCode) {
		$ticket = new tx_ptgsaconfmgm_ticket($ticketCode);

		$ret = $ticket->isValidTicket();
		if($ret === true) {
			$this->ticketCodes[$ticketCode] = $ticketCode;
			$this->saveCodesToSession();	
			$this->lastTicketInsertMsg = 'checkInOK';
			
			return true;
		} 
		
		$this->lastTicketInsertMsg = $ret;
		return false;
	}
	
	/**
	 * delete ticket code from session
	 * 
	 * @param $ticketCode
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function removeTicketCode($ticketCode) {
		unset($this->ticketCodes[$ticketCode]);
		$this->saveCodesToSession();
	}
	
	/**
	 * clear all ticket codes from session 
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function clearAllTicketCodes() {
		$this->ticketCodes = array();
		$this->saveCodesToSession();
	}
	
	/**
	 * set tickets booked and reset the session
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function bookTicketsInDatabase() {
		
		foreach($this->ticketCodes as $ticketCode) {
			$ticket = new tx_ptgsaconfmgm_ticket($ticketCode);
			$ticket->set_checked_in(1);
			$ticket->set_goodies_received(1);
			$ticket->save();
		}
	}
	
	/**
	 * set the operationmode
	 * 
	 * @param $modeType
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function switchToMode($modeType) {
				
		tx_pttools_feUsersessionStorageAdapter::getInstance()->store('tx_ptgsaconfmgm_checkin_operationMode', $modeType);
		$this->operationMode = $modeType;
	}
	
	/**
	 * save ticketcodes to session
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 01.09.2009
	 */
	protected function saveCodesToSession() {	
		// save the codes as array
		tx_pttools_feUsersessionStorageAdapter::getInstance()->store('tx_ptgsaconfmgm_checkin_ticketCodes', $this->ticketCodes);
		tx_pttools_feUsersessionStorageAdapter::getInstance()->store('tx_ptgsaconfmgm_checkin_ticketListWhere', $this->getCheckInTicketCodesWhere());
		
	}
	
	
	protected function getCheckInTicketCodesWhere() {	
		
		$ticketCodes = $this->ticketCodes;
		$whereClauseSnippet = 0;
		
		if(count($ticketCodes) > 0) {
			
			$whereClauseSnippet = (int) array_pop($ticketCodes);
			
			foreach($ticketCodes as $code) {
				$whereClauseSnippet .= ' or article_code = ' . (int) $code;
			}
		}
		
		return $whereClauseSnippet;
	}
	
	
	
	/**
	 * read ticketcodes from session
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 01.09.2009
	 */
	protected function readCodesFromSession() {
		$this->ticketCodes = tx_pttools_feUsersessionStorageAdapter::getInstance()->read('tx_ptgsaconfmgm_checkin_ticketCodes');
		if(!is_array($this->ticketCodes)) $this->ticketCodes = array();
	}
	
	public function get_oprationMode() {
		return $this->operationMode();
	}
	
	public function get_lastTicketInsertMsg() {
		return $this->lastTicketInsertMsg;
	}
	
	/**
	 * get the markers to display the state
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function getStateMarkerArray() {
	
		$markerArray = array();
		$llArr = tx_pttools_div::readLLfile('EXT:pt_gsaconfmgm/locallang.xml');
			
		if($this->lastTicketInsertMsg !== false) {
			if($this->lastTicketInsertMsg == 'checkInOK') {
				$markerArray['stateClass'] = 'barcodeStateOK';
			} else {
				$markerArray['stateClass'] = 'barcodeStateError';
			}
			
			$markerArray['insertMsg'] = tx_pttools_div::getLLL($this->lastTicketInsertMsg, $llArr);
		}
		
		$markerArray['mode'] = $this->operationMode;
		return $markerArray;
	}
	
	
	// TODO : change this - filter by category ticket
	public static function getCheckinStats() {
		$list = array();
		
		// prepare query
		$select = 'COUNT( * ) AS `c` , `checked_in`';
		$from	= '`tx_ptgsaconfmgm_user_article_rel`';
		$where  = 'article_artnr like "AF-%"';
		$groupBy = 'checked_in';
		$orderBy = '';
		$limit = '';		
		
		
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        
        trace(tx_pttools_div::returnLastBuiltSelectQuery($GLOBALS['TYPO3_DB'], $select, $from, $where, $groupBy, $orderBy, $limit));
        if ($res == false) {
            throw new tx_pttools_exception('Query failed ' . __FUNCTION__, 1);
        }
		
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
           $list[$row['checked_in']] = $row['c']; 
        }
        
        return $list;		
	}
	
} 
?>