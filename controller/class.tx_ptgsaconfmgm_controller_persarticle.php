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

require_once t3lib_extMgm::extPath('pt_mvc') . 'classes/class.tx_ptmvc_controllerFrontend.php';
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_cart.php';  // GSA shop cart class
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'res/class.tx_ptgsaconfmgm_articleAsado.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'res/class.tx_ptgsaconfmgm_relatedAsado.php';

// Models
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_persarticleform.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_persrelatedform.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_articleConfigFactory.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'model/class.tx_ptgsaconfmgm_persarticle.php';

// Views
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'view/class.tx_ptgsaconfmgm_view_persarticleform.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm') . 'view/class.tx_ptgsaconfmgm_view_persrelatedform.php';

class tx_ptgsaconfmgm_controller_persarticle extends tx_ptmvc_controllerFrontend {
	
	protected $fieldPrefix = 'tx_ptgsaconfmgm_controller_persarticle';
	
	/**
	 * Show Default
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 16.04.2009
	 */
	public function defaultAction(){
		die('Action is not implemented');	
	}
	
	/**
	 * edit a single persData
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function editSinglePersDataAction() {
		tx_pttools_assert::loggedIn(array('No frontend user found.'));
		$model = new tx_ptgsaconfmgm_persarticleform($this->fieldPrefix, $this->params);
		$model->createFormFieldsByPersData();
		
		$view = $this->getView('persarticleform');
		$view->addItem($this->fieldPrefix, 'fieldprefix');
		$view->addItem($model, 'articleform');
		
		return $view->render();	
	}
	
	/**
	 * show personalize article form
	 * 
	 * @return unknown_type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 19.05.2009
	 */
	public function personalizeArticleAction() {
		$model = new tx_ptgsaconfmgm_persarticleform($this->fieldPrefix, $this->params);
		$model->createFormFieldsFromCartItems();
	
		$view = $this->getView('persarticleform');
		$view->addItem($this->fieldPrefix, 'fieldprefix');
		$view->addItem($model, 'articleform');
		
		return $view->render();
	}
	
	
	/**
	 * edit a single relatedData
	 * 
	 * @return html
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function editSingleRelDataAction() {
		tx_pttools_assert::loggedIn(array('No frontend user found.'));
		
		$model = new tx_ptgsaconfmgm_persrelatedform($this->fieldPrefix, $this->params);
		$model->createFormFieldsByRelData();
		
		$view = $this->getView('persrelatedform');
		$view->addItem($this->fieldPrefix, 'fieldprefix');
		$view->addItem($model, 'articleform');
		
		return $view->render();	
	}
	
	/**
	 * show personalize related form
	 * 
	 * @return html
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 19.05.2009
	 */
	public function personalizeRelatedAction() {
		$model = new tx_ptgsaconfmgm_persrelatedform($this->fieldPrefix, $this->params);
		$model->createFormFieldsFromCartItems();

		$view = $this->getView('persrelatedform');
		$view->addItem($this->fieldPrefix, 'fieldprefix');
		$view->addItem($model, 'articleform');
		
		return $view->render();
	}
	
	/**
	 *  Check the submited form and save it to the asado object if valid
	 * 
	 * @return html
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 19.05.2009
	 */
	public function submitPersonalizeRelatedAction() {
		
		$model = new tx_ptgsaconfmgm_persrelatedform($this->fieldPrefix, $this->params);
		
		if($model->validateForm($this->params)) {

			// form is valid			
			$this->saveRelatedData();
	
			$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsashop_pi2.');
			tx_pttools_div::localRedirect($this->pi_getPageLink($tsConf['shoppingcartPage'], '', array('tx_ptgsashop_pi1[persDataComplete]' => '1')));
			
		} else {
			$view = $this->getView('persrelatedform');
			$view->addItem(true, 'cond_displayMsgBox');
			
			$model->createFormFieldsFromCartItems();

			$view = $this->getView('persrelatedform');
			$view->addItem($this->fieldPrefix, 'fieldprefix');
			$view->addItem($model, 'articleform');
			$view->addItem($GLOBALS['TSFE']->loginUser, 'cond_UserLoggedIn');
		
			return $view->render();
		}
	}
	
	/**
	 * Check the submited form and save it to the asado object if valid
	 * 
	 * @return html code
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 16.04.2009
	 */
	public function submitPersonalizeArticleAction() {
		$model = new tx_ptgsaconfmgm_persarticleform($this->fieldPrefix, $this->params);
		
		if($model->validateForm($this->params)) {
			
			// form is valid
			$this->savePersArticleData();
			
			// if there are related articles go to the related form
			if (tx_ptgsaconfmgm_div::countArticleTypeInCart('persrelated') > 0) {
				return $this->personalizeRelatedAction();
				
			} else {
				$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsashop_pi2.');
				tx_pttools_div::localRedirect($this->pi_getPageLink($tsConf['shoppingcartPage'], '', array('tx_ptgsashop_pi1[persDataComplete]' => '1')));
			}
					
		} else {
			$view = $this->getView('persarticleform');
			$view->addItem(true, 'cond_displayMsgBox');
			
			$model->createFormFieldsFromCartItems();
			
			$view->addItem(__CLASS__, 'fieldprefix');
			$view->addItem($model, 'articleform');
			
			return $view->render();
		}
	}
	
	/**
	 * save the edited data
	 * 
	 * @return string html
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 02.06.2010
	 */
	public function submitSinglePersDataAction(){
		$model = new tx_ptgsaconfmgm_persarticleform($this->fieldPrefix, $this->params);
		
		if($model->validateForm($this->params)) {
			
			$persDataUid = (int) $this->params['persDataUid'];
			$persData = array_shift(array_shift($this->params['articles']));			
			
			$persArticle = new tx_ptgsaconfmgm_persarticle($persDataUid);
			tx_pttools_assert::isTrue(tx_ptgsaconfmgm_persarticleform::checkDataSetRights($persArticle->get_tx_ptgsashop_customer_uid()));
			
			$persArticle->setPersData($persData);
			$persArticle->save();
			
			tx_pttools_div::localRedirect($this->pi_getPageLink($this->params['listPageUid']));
				
		} else {
			$view = $this->getView('persarticleform');
			$view->addItem(true, 'cond_displayMsgBox');
			
			$model->createFormFieldsByPersData();
			
			$view->addItem(__CLASS__, 'fieldprefix');
			$view->addItem($model, 'articleform');
			
			return $view->render();
		}
	}
	
	
	/**
	 * save the edited data
	 * 
	 * @return string html
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 02.06.2010
	 */
	public function submitSingleRelDataAction(){
		$model = new tx_ptgsaconfmgm_persrelatedform($this->fieldPrefix, $this->params);
		
		if($model->validateForm($this->params)) {
			
			$relDataUid = (int) $this->params['relDataUid'];
			$relData = array_shift(array_shift($this->params['articles']));			
			
			$relArticle = new tx_ptgsaconfmgm_relarticle($relDataUid);
			tx_pttools_assert::isTrue(tx_ptgsaconfmgm_persrelatedform::checkDataSetRights($relArticle->get_tx_ptgsashop_customer_uid()));
			
			$relArticle->setRelArticleData($relData);
			$relArticle->save();
			
			tx_pttools_div::localRedirect($this->pi_getPageLink($this->params['listPageUid']));
				
		} else {
			$view = $this->getView('relarticleform');
			$view->addItem(true, 'cond_displayMsgBox');
			
			$model->createFormFieldsByPersData();
			
			$view->addItem(__CLASS__, 'fieldprefix');
			$view->addItem($model, 'articleform');
			
			return $view->render();
		}
	}
	
	protected function saveRelatedData() {
		$cartObj = tx_ptgsashop_cart::getInstance();
		
		foreach($cartObj as $artKey => $artObj) {
			$artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj); 
			
			if($artConf->isRelated() && is_array($this->params['articles'][$artObj->get_id()])) {
					
				$asado = new tx_ptgsaconfmgm_relatedAsado($artObj->get_id());
				
				foreach($this->params['articles'][$artObj->get_id()] as $relArticleData) {
					$asado->addRelArticleInstance($relArticleData);
				}
				
				$artObj->set_applSpecDataObj($asado);

			}
		}
		
		$cartObj->store();
	}
	
	
	/**
	 * save the personalized data to the asado
	 * 
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 21.04.2009
	 */
	protected function savePersArticleData() {
		
		$cartObj = tx_ptgsashop_cart::getInstance();
		
		foreach($cartObj as $artKey => $artObj) {

			$artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($artObj);
			
			if(is_array($this->params['articles'][$artObj->get_id()])) {
				
				if($artConf->isPersonalizable()) {
					$artAsado = new tx_ptgsaconfmgm_articleAsado();

					$artAsado->setArticleData($this->params['articles'][$artObj->get_id()]);
								
					$artObj->set_applSpecDataObj($artAsado);
				}
				
			}		
		}
				
		$cartObj->store();
		
	}
}
?>