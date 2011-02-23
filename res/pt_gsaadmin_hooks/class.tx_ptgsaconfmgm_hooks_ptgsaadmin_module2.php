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

require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general static library class

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_articleConfigFactory.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_eventAccessor.php';

class tx_ptgsaconfmgm_hooks_ptgsaadmin_module2 {
	
	protected $articleConfigField = 2;
	
	/**
	 * Hook to alter the article form and add some conference related fields
	 * 
	 * @param $params array
	 * @param $object tx_ptgsaadmin_module2
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 12.05.2010
	 */
	public function returnArticleForm_completeForm($params, tx_ptgsaadmin_module2 $object) {
		
		$llArray = tx_pttools_div::readLLfile(t3lib_extMgm::extPath('pt_gsaconfmgm'). 'locallang.xml');
		
		/**
		 * change existing elements
		 */
		$params['formObj']->removeElement('userField02');
		$params['formObj']->removeElement('userField03');
		$params['formObj']->getElement('userField01')->setLabel(tx_pttools_div::getLLL('gsaAdmin_artForm_sorting', $llArray));
		
		/**
		 * add new elements
		 */
		$params['formObj']->addElement('header', 'artHeader6', tx_pttools_div::getLLL('gsaAdmin_artForm_header_confMGM', $llArray));
		
		
		$events = array(0 => tx_pttools_div::getLLL('gsaAdmin_artForm_noEventSelected', $llArray));
		$events = array_merge($events, tx_ptgsaconfmgm_eventAccessor::getInstance()->getTitleList());
		$params['formObj']->addElement('select', 'articleEvent', tx_pttools_div::getLLL('gsaAdmin_artForm_articleEvent', $llArray), 
										$events);
		
		$params['formObj']->addElement('advcheckbox', 'physicalFlag', tx_pttools_div::getLLL('gsaAdmin_artForm_physicalFlag', $llArray), '', '', array('0', '1'));
		$params['formObj']->addElement('advcheckbox', 'personalizableFlag', tx_pttools_div::getLLL('gsaAdmin_artForm_personalizableFlag', $llArray), '', '', array('0', '1'));
		$params['formObj']->addElement('advcheckbox', 'persrelatedFlag', tx_pttools_div::getLLL('gsaAdmin_artForm_persrelatedFlag', $llArray), '', '', array('0', '1'));
		$params['formObj']->addElement('advcheckbox', 'voucherRequiredFlag', tx_pttools_div::getLLL('gsaAdmin_artForm_voucherRequiredFlag', $llArray), '', '', array('0', '1'));
	}
	
	/**
	 *  Save the additional fields to the article Object
	 *  
	 * @param $params array
	 * @param $object tx_ptgsaadmin_module2
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 12.05.2010
	 */
	public function createArticleFromFormData_processData($params, tx_ptgsaadmin_module2 $object) {
		
		$articleConfig = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($params['articleObj']);
		$articleConfig->setEventUid(t3lib_div::GPvar('articleEvent'));
		$articleConfig->setPersonalizable(t3lib_div::GPvar('personalizableFlag'));
		$articleConfig->setPhysical(t3lib_div::GPvar('physicalFlag'));
		$articleConfig->setRelated(t3lib_div::GPvar('persrelatedFlag'));
		$articleConfig->setVoucherRequired(t3lib_div::GPvar('voucherRequiredFlag'));
				
		$params['articleObj']->set_userField02($articleConfig->getSerializedConfig());
		$params['articleObj']->set_userField03($articleConfig->getEventUid());
	}
	
	/**
	 * Set the article values on load
	 * 
	 * @param $params array 
	 * @param $object tx_ptgsaadmin_module2
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 12.05.2010
	 */
	public function loadArticleDefaults($params, tx_ptgsaadmin_module2 $object) {
		$articleConfig = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($params['articleObj']);
		
		$params['articleDataArr']['physicalFlag'] = $articleConfig->isPhysical();
		$params['articleDataArr']['personalizableFlag'] = $articleConfig->isPersonalizable();
		$params['articleDataArr']['persrelatedFlag'] = $articleConfig->isRelated();
		$params['articleDataArr']['voucherRequiredFlag'] = $articleConfig->isVoucherRequired();
		$params['articleDataArr']['articleEvent'] = $articleConfig->getEventUid();
		
	}
}
?>