<?php

/***************************************************************
*  Copyright notice
*
*  
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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_event.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_articleConfigFactory.php';

class tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice {

	
	/**
	 * Add the additional markers for the invoice
	 * @param $ref tx_ptgsapdfdocs_invoice
	 * @param $markerArray array
	 * @return array marker array
	 * @author Christoph Ehscheidt
	 * @since 02.06.2010
	 */
	public function displayShoppingCart_MarkerArrayHook(tx_ptgsapdfdocs_invoice $ref, $markerArray) {
		

		$orderObj = $ref->get_orderObj();
		
		$markerArray = $this->generateEventMarker($orderObj, $markerArray);
		$markerArray = $this->overrideBillingAddress($orderObj, $markerArray);
		
		return $markerArray;
	}
	
	protected function overrideBillingAddress($orderObj, $markerArray) {
		$markerArray['billingAddress'] = '';
		
		$billingObj = $orderObj->get_billingAddrObj();
        
		// get post-fields without "company" e.g. post2 - post7
        for ($i=2; $i<=7; $i++) {
            $getter = 'get_post'.$i;
            $tmp = $billingObj->$getter();
            if ($tmp) { 
                $address[] = $tmp;
            }
        }
       
        // get country field
        $address[] = $billingObj->get_country();
        
        $markerArray['billingAddress'] = implode(chr(10), $address);  
        
        return $markerArray;
	}
	
	protected function generateEventMarker($orderObj, $markerArray) {
		$articles = $orderObj->get_deliveryCollObj()
		->getItemByIndex(0)
		->get_articleCollObj();

		$i=0;
		foreach($articles as $article) {
						
			$eventId = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($article)->getEventUid();

			if($eventId) $event = new tx_ptgsaconfmgm_event($eventId);
			
			if(is_a($event, 'tx_ptgsaconfmgm_event') && !$event->isNew()) {
				
				$start = $event->get_startdate();
				$end = $event->get_enddate();
				
				$startStr = date('m/d/Y', $start);
				$endStr = date('m/d/Y', $end);
				
				$title = $event->get_title();
				$descr = $event->get_description();
				$venue = $event->get_venue();
				
				
			//	$markerArray['events'][$i] = "$title; $descr; $startStr - $endStr; $venue";
				$markerArray['events'][$i]['title'] = $title;
				$markerArray['events'][$i]['start'] = $startStr;
				$markerArray['events'][$i]['end'] = $endStr;
				$markerArray['events'][$i]['description'] = $descr;
				$markerArray['events'][$i]['venue'] = $venue;
				$i++;
			}


		}
		return $markerArray;
	}
	
	/**
	 * Check if the curent user is conference-admin and therefore allowed to see all invoices
	 * 
	 * @param $previousDecision bool
	 * @return bool
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 15.07.2010
	 */
	public function checkAccessAllowedHook($previousDecision) {
		return true;
	}
	
}

?>