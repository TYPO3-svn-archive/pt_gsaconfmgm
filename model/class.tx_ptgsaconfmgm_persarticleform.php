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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_formchecker.php';

require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_cart.php';  // GSA shop cart class
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_article.php';  // GSA shop cart class

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_persArticleInfoAccessor.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_voucher.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticle.php';
 		
//require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_persArticleInfo.php';


/**
 * Model with the Cart Data
 * @author   Daniel Lienert <lienert@punkt.de>
 *
 */
class tx_ptgsaconfmgm_persarticleform implements tx_pttools_iTemplateable {
	 	
    protected $articleFormFields = array();
       
    protected $formMarker = array();
    
    protected $formErrors;
    
    protected $formErrorDescription;
    
    protected $fieldPrefix;
    
    protected $params;
    
    protected $articlePostData = array();
    
    /**
     * @var true if called from edit ticket data
     */
    protected $editMode = false;
    
    /**
     * 
     * 
     * @return unknown_type
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 16.04.2009
     */
    public function __construct($fieldPrefix = '', array $params = null) {
    	$this->fieldPrefix = $fieldPrefix;
    	$this->params = $params;
    	
    	if(is_array($params['articles'])) {
    		$this->articlePostData = $params['articles'];	
    	}
    	
    	$this->fillDefaultFormMarker();
    }
 
    
    protected function fillDefaultFormMarker() {
    	$this->formMarker = array(
    		'persCheckoutButton_name' => 'persCheckoutButton',
    		'persCheckoutButton_label' => 'Next',
    		
    		'listPageUid' => $this->params['listPageUid'],
    		
    		'controllerAction' => 'submitPersonalizeArticle',
    	);
    }
    
    /**
     * @see res/abstract/tx_pttools_iTemplateable#getMarkerArray()
     */
	public function getMarkerArray() {
		$this->formMarker['articles'] = $this->articleFormFields;
		return $this->formMarker;
	}
    
	/**
	 * Create persarticle form fields vor all articles in the 
	 * current cart with persarticle flag 
	 *  
	 * @param $postParams
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 26.05.2010
	 */
	public function createFormFieldsFromCartItems() {
		
		$cartObj = tx_ptgsashop_cart::getInstance();
		
		foreach($cartObj as $artKey => $artObj) {
			
			if(tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj)->isPersonalizable()) {
				$fields = $this->createFormFieldsForArticleObject($artKey, $artObj);
				$this->articleFormFields[$artObj->get_id()] = $fields;	
			}
		}
	}
	
	
	/**
	 * check if the cuurent user has the right to load a specific persdata/persrelated dataset
	 * 
	 * @param $dataSetGsaCustomer
	 * @return bool
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 02.06.2010
	 */
	public static function checkDataSetRights($dataSetGsaCustomer) {
		if($dataSetGsaCustomer == 0) return false;
		if($dataSetGsaCustomer != $GLOBALS['TSFE']->fe_user->user['tx_ptgsauserreg_gsa_adresse_id']) return false;
		
		return true;
	}
	
	
	/**
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function createFormFieldsByPersData() {
		
		$persDataUid = (int)$this->params['persDataUid'];
		$persArticle = new tx_ptgsaconfmgm_persarticle($persDataUid);
		tx_pttools_assert::isTrue(self::checkDataSetRights($persArticle->get_tx_ptgsashop_customer_uid()),array('info' => 'You are not allowed to load the dataset'));
		
		// form Markers
		$this->editMode = true;
		$this->formMarker['persCheckoutButton_label'] = 'Save';
		$this->formMarker['controllerAction'] = 'submitSinglePersData';
		$this->formMarker['persDataUid'] = $persDataUid;
		
		
		$tempArticle = new tx_ptgsashop_article(0);
		$tempArticle->loadFromOrderArchive($persArticle->get_tx_ptgsashop_orders_articles_uid());
		$shopArticle = new tx_ptgsashop_article($tempArticle->get_id());
		$shopArticle->set_quantity(1, false);
		
		if(!is_array($this->articlePostData[$shopArticle->get_id()][0])) {
			$this->articlePostData[$shopArticle->get_id()][0] = $persArticle->getPersData();
		}			
		
		$fields = $this->createFormFieldsForArticleObject($shopArticle->get_id(), $shopArticle);
		$this->articleFormFields[$shopArticle->get_id()] = $fields;	
	}
	
	/**
	 * Create a formular for the given article object
	 * 
	 * @param $artObj tx_ptgsashop_baseArticle the article to create the form for
	 * @param $postParams array previously posted data
	 * @return $artArr array of article lines
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 26.05.2010
	 */
    protected function createFormFieldsForArticleObject($artKey, tx_ptgsashop_baseArticle $artObj) {
   			
		$artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj);
		$voucherRequired = $artConf->isVoucherRequired() && !$this->editMode;
		
		$artArr = array(
			'id' => $artObj->get_id(),
			'name' => $artObj->get_match1(),
			'quantity' => $artObj->get_Quantity(),
			
			// do we need a voucher to purchase this article?
			'voucher_required' => $voucherRequired,
		);
		
		
		// personalize article lines
		for($i = 0; $i < $artObj->get_Quantity(); $i++) {
		   
			$artArr['perslist'][$i] = $this->getPersArticleLine($artKey, $i);
			$artArr['perslist'][$i]['dscount'] = count($artArr['perslist'][$i]['infolist'])+3;
			if($voucherRequired) {
				$artArr['perslist'][$i]['dscount']++;
			}
		}
		return $artArr;
		
	}
 	
	/**
	 * Create a persarticle line
	 * 
	 * @param $artKey 
	 * @param $lineId
	 * @return array of markervalues
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
 	protected function getPersArticleLine($artKey, $lineId) {
 		$persLine = array(
 			'id' => $lineId,
   			'viewId' => ($lineId+1),
   			'values' => $this->articlePostData[$artKey][$lineId], 
   			'class_firstname' => ($this->formErrors[$artKey][$lineId]['firstname'] ? 'tx_ptgsaconfmgm_fielderror' : ''),
   			'class_lastname' => ($this->formErrors[$artKey][$lineId]['lastname'] ? 'tx_ptgsaconfmgm_fielderror' : ''),
   			'class_email' => ($this->formErrors[$artKey][$lineId]['email'] ? 'tx_ptgsaconfmgm_fielderror' : ''),
   			'class_vouchercode' => ($this->formErrors[$artKey][$lineId]['vouchercode'] ? 'tx_ptgsaconfmgm_fielderror' : ''),
			'errmsg_vouchercode' => $this->formErrorDescription[$artKey][$lineId]['vouchercode'],
   		
   			// add additional article info fields   					
   			'infolist' => tx_ptgsaconfmgm_persArticleInfoAccessor::getInstance()->getArticleInfoEntities($artKey),
 		);
 		
 		return $persLine;
 	}
 	
 	/**
 	 * 
 	 * 
 	 * @param $postParams
 	 * @return boolean true if the form has no error
 	 * @author Daniel Lienert <lienert@punkt.de>
 	 * @since 21.04.2009
 	 */
 	public function validateForm ($postParams) {
		 		
 		$fc = new tx_pttools_formchecker();
 		$this->formErrors = array();
 		
 		// check each article
 		foreach($postParams['articles'] as $artKey => $article) {
 			foreach($article as $persKey => $persLine) {
 				if($fc->checkText($persLine['firstname'], 'Firstname', 1) != '') $this->formErrors[$artKey][$persKey]['firstname'] = true;
 				if($fc->checkText($persLine['lastname'], 'Lastname', 1) != '') $this->formErrors[$artKey][$persKey]['lastname'] = true;
 				if($fc->checkEmail($persLine['email'], 'eMail', 1) != '') $this->formErrors[$artKey][$persKey]['email'] = true;

 				// vouchercode
 				if(isset($persLine['vouchercode'])) $this->checkForValidVoucherCode($persLine['vouchercode'], $artKey, $persKey);
 			}
 		}
 		
 		if (count($this->formErrors) > 0) return false;
 		
 		return true;
 	}
	
 	/**
 	 * Check if the given VoucherCode is valid
 	 * 
 	 * @param $articleID	ID of the current article
 	 * @param $voucherCode	Given Voucher Code
 	 * @return boolean 
 	 * @author Daniel Lienert <lienert@punkt.de>
 	 * @since 19.06.2009
 	 */
 	protected function checkForValidVoucherCode($voucherCode, $artKey, $persKey) {
 				
 		$voucher = new tx_ptgsaconfmgm_voucher(0,$voucherCode);
 		 		
 		$voucherArticles = explode(',', $voucher->get_articleConfinement());
 		
 		// check the voucher
 		if(!($voucher->get_uid() > 0)) {
 			$this->setFormError($artKey, $persKey, 'vouchercode', true, 'paErrVoucherNotValid');
 		} elseif($voucher->get_isEncashed()) {
 			$this->setFormError($artKey, $persKey, 'vouchercode', true, 'paErrVoucherAlreadyEncashed');
 		} elseif(!in_array($artKey,$voucherArticles)) {
 			$this->setFormError($artKey, $persKey, 'vouchercode', true, 'paErrVoucherNotValidForThisArticle');
 		} elseif(strtotime($voucher->get_expiryDate()) - time() < 0) {
 			$this->setFormError($artKey, $persKey, 'vouchercode', true, 'paErrVoucherExpired');
 		}
 	}
 	
 	/**
 	 * Function to set the objects form-error arrays
 	 * 
 	 * @param $artKey		string Article Key
 	 * @param $persKey		string Key of the personal article line
 	 * @param $isError		boolean 
 	 * @param $description	string Error description
 	 * @return unknown_type
 	 * @author Daniel Lienert <lienert@punkt.de>
 	 * @since 22.06.2009
 	 */
 	protected function setFormError($artKey, $persKey, $fieldName, $isError, $description='') {
 		if($isError == true) {
 			$this->formErrorDescription[$artKey][$persKey][$fieldName] = $description;
 		} else {
 			unset($this->formErrorDescription[$artKey][$persKey][$fieldName]);
 		}
 		
		$this->formErrors[$artKey][$persKey][$fieldName] = $isError;
 	}
}
?>