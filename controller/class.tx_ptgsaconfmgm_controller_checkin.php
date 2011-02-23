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

// Controllers
require_once t3lib_extMgm::extPath('pt_mvc') . 'classes/class.tx_ptmvc_controllerFrontend.php';
require_once t3lib_extMgm::extPath('pt_list').'controller/class.tx_ptlist_controller_list.php';

// Models
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_checkin.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_stocklist.php';

// Views
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'view/class.tx_ptgsaconfmgm_view_registerbarcodes.php';

class tx_ptgsaconfmgm_controller_checkin extends tx_ptmvc_controllerFrontend {
	
	
	 /*
     * @var     string  class to use for lists integrated as sub-controllers
     */
    const SUBCONTROLLER_LIST_CLASS = 'EXT:pt_list/model/typo3Tables/class.tx_ptlist_typo3Tables_list.php:tx_ptlist_typo3Tables_list';
	
    protected $checkInObj;
    
    /**
     * 
     * @return unknown_type
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 08.09.2009
     */
    public function __construct() {
    	$this->checkInObj = new tx_ptgsaconfmgm_checkin();
    	
    	parent::__construct();
    }
    
	/**
	 * Show Default
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 16.04.2009
	 */
	public function defaultAction(){
	
		return $this->doAction('showBarcodeInput');
	}
	
	/**
	 * Show the barcode input form and a list of already scanned tickets
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 01.09.2009
	 */
	public function showBarcodeInputAction() {
				
		$view = $this->getView('registerbarcodes');
		$view->addItem($this->pi_getPageLink($GLOBALS['TSFE']->id), 'faction_registerocode');
		
		$view->addItem($this->getSubcontrollerList('listTicketData'), 'listTicketData', false);  // do not filter HTML here since this subcontroller list is already rendered as HTML		
		
		$stockList = new tx_ptgsaconfmgm_stocklist();
		$view->addItem($stockList->getStockList(), 'stocklist');

		$view->addItem(tx_ptgsaconfmgm_checkin::getCheckinStats(), 'checkinstats');
		
		$stateMarkerArray = $this->checkInObj->getStateMarkerArray();
		$view->addItem($stateMarkerArray,'state');
		
		return $view->render();		
	}

	
	/**
	 * 
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
	public function submitBarcodeAction() {
		$ticketCode = $this->params['ticketcode'];
			
		
		if (is_numeric($ticketCode)) {
			$this->checkInObj->addRemoveTicketCode($ticketCode);
		} else {
			
			$ticketCode = strtoupper($ticketCode);
			
			switch($ticketCode) {
				case 'SUBMIT':
					$this->checkInObj->bookTicketsInDatabase();
					
					$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsaconfmgm.');
					//tx_pttools_div::localRedirect($this->pi_getPageLink($tsConf['checkInPrintoutPage']));
					
					break;
				case 'RESET':
					$this->checkInObj->clearAllTicketCodes();
					break;
				case 'STORNO':
				case 'INSERT':
					$this->checkInObj->switchToMode($ticketCode);
					break;
			}
		}
		
		return $this->doAction('showBarcodeInput');
	}
 
	/**
	 * return the html output of a list sub controller
	 * 
	 * @param $listId
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.09.2009
	 */
    protected function getSubcontrollerList($listId) {
              
        // create sub-controller list
        $listController = new tx_ptlist_controller_list(array(
            'listClass' => self::SUBCONTROLLER_LIST_CLASS,
            'listId' => $listId,
            'pluginMode' => 'list',
            'subControllerPrefixPart' => 'MY_'.$listId.'_prefix_MY',
        ));
        
        // pass cObj to sub-controller by cloning the parent one and removing the parent's flexform config
        $listController->cObj = clone $this->cObj;  
        unset($listController->cObj->data['pi_flexform']);
        $listHtml = $listController->main();
        
        return $listHtml;
        
    }
}
?>