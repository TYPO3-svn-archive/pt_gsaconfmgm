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
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_sessionFeCustomer.php';  // GSA FE Customer

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/staticlib/class.tx_ptgsaconfmgm_div.php'; 
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_persArticleInfoAccessor.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_articleAsado.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_articleConfigFactory.php';

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticleCollectionFactory.php';



/**
 * PersRelatedForm Model
 * @author   Daniel Lienert <lienert@punkt.de>
 */
class tx_ptgsaconfmgm_persrelatedform implements tx_pttools_iTemplateable {
	 
	
	protected $articleFormFields = array();
       
    protected $formMarker = array();
	
    protected $articleArray;
       
    protected $formErrors;
    
    protected $fieldPrefix;
    
    /**
     * Persdata SelectArray by Event
     * @var array
     */
    protected $persDataSelectArray;
    
    /**
     * used to right objectHash for DS-ID
     * @var arary
     */
    protected $idToObjectHashMap;
    
    
    protected $articlePostData = array();
    
    /**
     * @var true if called from edit ticket data
     */
    protected $editMode = false;
    
    protected $params;
    
    
    
    /**
     * @return unknown_type
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 16.04.2009
     */
    public function __construct($fieldPrefix = '', $params) { 	
    	$this->fieldPrefix = $fieldPrefix;
    	$this->params = $params;
    	
    	$this->fillDefaultFormMarker();
    }
 
    
	protected function fillDefaultFormMarker() {
    	$this->formMarker = array(
    		'relCheckoutButton_name' => 'relatedCheckoutButton',
    		'relCheckoutButton_label' => 'Next',
    		
    		'listPageUid' => $this->params['listPageUid'],
    		
    		'controllerAction' => 'submitPersonalizeRelated',
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
	 * Create persrelated form fields vor all articles in the 
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
			
			if(tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj)->isRelated()) {
				$fields = $this->createFormFieldsForArticleObject($artKey, $artObj);
				$this->articleFormFields[$artObj->get_id()] = $fields;	
			}
		}
	}
	
	
	/**
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function createFormFieldsByRelData() {
		
		$relDataUid = (int)$this->params['relDataUid'];
		$relArticle = new tx_ptgsaconfmgm_relarticle($relDataUid);
		tx_pttools_assert::isTrue(self::checkDataSetRights($relArticle->get_tx_ptgsashop_customer_uid()),array('info' => 'You are not allowed to load the dataset'));
		
		// form Markers
		$this->editMode = true;
		$this->formMarker['relCheckoutButton_label'] = 'Save';
		$this->formMarker['controllerAction'] = 'submitSingleRelData';
		$this->formMarker['relDataUid'] = $relDataUid;
		
		$tempArticle = new tx_ptgsashop_article(0);
		$tempArticle->loadFromOrderArchive($relArticle->get_tx_ptgsashop_orders_articles_uid());
		$shopArticle = new tx_ptgsashop_article($tempArticle->get_id());
		$shopArticle->set_quantity(1, false);

		$artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($shopArticle);
		
		if(!$this->articlePostData[$shopArticle->get_id()][0]['relatedArticle']) {			
			// get the object Hash 
			
			$this->getAllPersArticles($artConf->getEventUid());
			$this->articlePostData[$shopArticle->get_id()][0] = $relArticle->getRelArticleData();
			$this->articlePostData[$shopArticle->get_id()][0]['relatedArticle'] = $this->idToObjectHashMap[$relArticle->get_persdata()];
			
		}	
		
		$fields = $this->createFormFieldsForArticleObject($shopArticle->get_id(), $shopArticle);
		$this->articleFormFields[$shopArticle->get_id()] = $fields;
	}
	
	
	/**
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function createFormFieldsByRelatedData() {
		$relDataUid = (int)$this->params['relDataUid'];
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
   public function createFormFieldsForArticleObject($artKey, tx_ptgsashop_baseArticle $artObj) {
   		 			
	   $artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj);
		
		$artArr = array(
		  'id' => $artObj->get_id(),
		  'name' => $artObj->get_match1(),
		  'quantity' => $artObj->get_Quantity(),
		  'relatedSelectList' => $this->getAllPersArticles($artConf->getEventUid()),
		  );
	
		
		// personalize article lines
	   for($i = 0; $i < $artObj->get_Quantity(); $i++) {
	   	
	   	// basic user data
	   	$artArr['perslist'][$i] = $this->getPersRelatedLine($artKey, $i);
	   	$artArr['perslist'][$i]['dscount'] = count($artArr['perslist'][$i]['infolist'])+1;
	   }
	   
	   
	   return $artArr;

 	}
 	
 	protected function getPersRelatedLine($artKey, $lineId) {
 		$relatedLine= array(
   			'id' => $lineId,
   			'viewId' => ($lineId+1),
   			'values' => $this->articlePostData[$artKey][$lineId], 
   			'class_userselect' => ($this->formErrors[$artKey][$lineId]['relatedArticle'] ? 'tx_ptgsaconfmgm_fielderror' : ''),
   				
   			// add additional article info fields   					
   			'infolist' => tx_ptgsaconfmgm_persArticleInfoAccessor::getInstance()->getArticleInfoEntities($artKey),
   		);
   		
   		return $relatedLine;
 	}
 	
 	/** 
 	 * Return the selct array of the given event - sorted by Lastname,Firstname
 	 * @param $eventId int
 	 * @return array combined array of curent and former persArticles
 	 * @author Daniel Lienert <daniel@lienert.cc>
 	 */
  	public function getAllPersArticles($eventID) {
  		
  		if(!$this->persDataSelectArray[$eventID]) {
	  		$persArticles = array_merge($this->getCurrentPersArticles($eventID), $this->getEventPersArticles($eventID));

	  		foreach($persArticles as $key => $persData) {
	  			$sortArray[$key] = $persData['lastname'].$persData['firstname'];
	  		}
	  		
	  		array_multisort($sortArray, SORT_ASC, $persArticles);
	  		
	  		$this->persDataSelectArray[$eventID] = $persArticles;	
  		}
  		
  		return $this->persDataSelectArray[$eventID];
  		
  	}
 	
  	/**
  	 * Load persArticles from former purchases of this user and this event
  	 * @param $eventId int
  	 * @return array
  	 * @author Daniel Lienert <daniel@lienert.cc>
  	 */
  	protected function getEventPersArticles($eventId) {
  		
  		$persArticleList = array();
  		
  		$customerID = tx_ptgsashop_sessionFeCustomer::getInstance()->get_gsaCustomerObj()->get_gsauid();
  		if(!$customerID) return $persArticleList;
  		
  		$persArticleCollection = tx_ptgsaconfmgm_persarticleCollectionFactory::createCollectionFormDatabase($eventId, $customerID);

  		foreach($persArticleCollection as $persArticleId => $persArticle) {
  			$this->idToObjectHashMap[$persArticle->get_uid()] = $persArticleId;
 			$persArticleList[$persArticleId] = $persArticle->get_lastname() . ', ' . $persArticle->get_firstname();
 		}
 		
 		return $persArticleList;
  	}
  	
 	
 	/**
 	 * Get all persArticles, defined in this session
 	 * 
 	 * @param $eventId int 
 	 * @return array
 	 * @author Christoph Ehscheidt <ehscheidt@punkt.de>, Daniel Lienert <lienert@punkt.de>
 	 */
 	protected function getCurrentPersArticles($eventId) {
 		$persArticleList = array();
 		
 		$cart = tx_ptgsashop_cart::getInstance();

 		foreach($cart as $articleId => $article) {
 			
 			if(tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($article)->getEventUid() == $eventId) {
	 			$asado = $article->get_applSpecDataObj();
	 			
	 			if($asado instanceof tx_ptgsaconfmgm_articleAsado) {
	 		
	 				$persarticles = $article->get_applSpecDataObj()->get_personArticleCollection();
	 				foreach($persarticles as $persArticleId => $persArticle) {
	 					$persArticleList[$persArticleId] = $persArticle->get_lastname() . ', ' . $persArticle->get_firstname();
	 				}
	 			}	
 			}
 		}

 		return $persArticleList;
 	}
 	
 	/**
 	 * 
 	 * 
 	 * @param $postParams
 	 * @return boolean true if form has no error
 	 * @author Daniel Lienert <lienert@punkt.de>
 	 * @since 21.04.2009
 	 */
 	public function validateForm ($postParams) {

 		$fc = new tx_pttools_formchecker();
 		$this->formErrors = array();
 		
 		foreach($postParams['articles'] as $artKey => $article) {
 			foreach($article as $persKey => $persLine) {
 					
 				if($fc->checkPulldown($persLine['relatedArticle'], 'Related Article', 1) != '') $this->formErrors[$artKey][$persKey]['relatedArticle'] = true;
 			}
 		}
	
 		if (count($this->formErrors) > 0) return false;
 		
 		return true;
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
 	
}
?>