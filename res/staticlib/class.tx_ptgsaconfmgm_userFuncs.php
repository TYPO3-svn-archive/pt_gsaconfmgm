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

require_once t3lib_extMgm::extPath('pt_tools') . 'res/objects/class.tx_pttools_feUsersessionStorageAdapter.php'; // general static library class
require_once t3lib_extMgm::extPath('pt_tools') . 'res/objects/class.tx_pttools_smartyAdapter.php';

require_once t3lib_extMgm::extPath('pt_list') . 'controller/filter/options/class.tx_ptlist_controller_filter_options_group.php'; // general static library class

/**
 * static function to call from typoscript as user functions
 *
 * $Id:$
 *
 * @package      	TYPO3
 * @subpackage  
 * @author         	Daniel Lienert <lienert@punkt.de>
 * @since           08.09.2009
 */
class user_tx_ptgsaconfmgm_userFuncs {
	
	/**
	 * get WhereClauseSnippet for the checkin ticketlist
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public static function getCheckInTicketCodesWhere() {
		
		$ticketCodes = tx_pttools_feUsersessionStorageAdapter::getInstance()->read('tx_ptgsaconfmgm_checkin_ticketCodes');
			
		if(is_array($ticketCodes) && count($ticketCodes) > 0) {
			$whereClauseSnippet = '';

			foreach($ticketCodes as $key => $value) {
				$whereClauseSnippet .= ' or article_code = ' . (int) $key;
			}
		}
		
		return $whereClauseSnippet;
	}
	
	/**
	 * Return a SQL Snippet build by the Session Variable from the Event Selector 
	 *  
	 * @return String SQL Snippet
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 07.07.2010
	 */
	public static function getSelectedEvent() {
		
		
		$serializedFilterCollection = tx_pttools_sessionStorageAdapter::getInstance()->read($GLOBALS['TSFE']->fe_user->user['uid'] . '_eventSelector_filter', false);
		$filterCollection = unserialize($GLOBALS['TSFE']->fe_user->user['uid']);

		$sqlSnippet = ' ';
		
		if(is_a($filterCollection, 'tx_ptlist_filterCollection')) {
			$filterValue = array_pop($filterCollection['groupEvent']->get_value());
			
			if(trim($filterValue)) {
				$sqlSnippet = ' AND events.title = "' . mysql_real_escape_string($filterValue) . '"';	
			}	
		}

		return $sqlSnippet;
	}
	
	/**
	 * Render the Attendee Information to display in a single pt_list column
	 * 
	 * @param $params
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 07.07.2010
	 */
	public static function renderAttendeeInformation(array $params) {
		$userInfoGrouped = $params['values']['userInfoGrouped'];
		$infoArray = explode(';', $userInfoGrouped);
		$infos = array();
		
		foreach($infoArray as $info) {
			$infoChunks = explode(',', $info);
			if(trim($infoChunks[1]) || trim($infoChunks[2])) {
				$infos[$infoChunks[0]] = trim($infoChunks[2]) ? trim($infoChunks[2]) : trim($infoChunks[1]);	
			}
		}
		
		// render with Smarty
		$smarty = new tx_pttools_smartyAdapter('pt_gsaconfmgm');
		$smarty->assign('infos', $infos);
		
		$templateFile = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsaconfmgm.ptlist.attendeeinformation.template');
		$filePath = $smarty->getTplResFromTsRes($templateFile);

		return  $smarty->fetch($filePath);		
	}
	
	/**
	 * Get the PDF Download Link
	 * 
	 * @param $params
	 * @return string
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 15.07.2010
	 */
	public static function getInvoicePDFLink(array $params) {
		require_once t3lib_extMgm::extPath('pt_gsapdfdocs') . 'res/class.tx_ptgsapdfdocs_div.php';
		
		$invoiceNo = $params['values']['invoiceNo'];
		return tx_ptgsapdfdocs_div::urlToInvoice($invoiceNo);
	}
	
	
}
?>