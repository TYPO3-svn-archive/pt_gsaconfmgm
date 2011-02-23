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


require_once t3lib_extMgm::extPath('pt_gsashop').'pi1/class.tx_ptgsashop_pi1.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/staticlib/class.tx_ptgsaconfmgm_div.php'; 

require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general static library class

/**
 * Debugging config for development
 */
#$GLOBALS['trace'] = 1;  // (int) trace options @see tx_pttools_debug::trace() [for local temporary debugging use only, please COMMENT OUT this line if finished with debugging!]
#$errStrict = 1; // (bool) set strict error reporting level for development (requires $trace to be set to 1)  [for local temporary debugging use only, please COMMENT OUT this line if finished with debugging!]


/**
 * 
 * 
 * @author      Daniel Lienert <lienert@punkt.de>
 * @since       14.04.2009
 * @package     TYPO3
 * @subpackage  
 */
class tx_ptgsaconfmgm_hooks_ptgsashop_pi1 extends tx_ptgsashop_pi1 {
	
	/**
	 * Change the cart_checkout action 
	 * 
	 * @param $pObj
	 * @param $markerArray
	 * @return manipulated markerArray
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 15.04.2009
	 */
	public function displayShoppingCart_MarkerArrayHook(tx_ptgsashop_pi1 $pObj, $markerArray) {
		
		$markerArray['fname_checkoutButton'] = $pObj->prefixId.'[article_personalize_button]';
		$markerArray['fname_updSubmittedButton'] = $pObj->prefixId . '[cart_upd_submitted_no_redir]';
			
		return $markerArray;
	}
	

	public function exec_checkout_loginHook(tx_ptgsashop_pi1 $pObj) {
		
	}
	
	
	/**
	 * Register new action to maincontroller
	 * 
	 * @param $pObj tx_ptgsashop_pi1
	 * @return Conetent String
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 15.04.2009
	 */
	protected function mainControllerHook(tx_ptgsashop_pi1 $pObj) {
		$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsaconfmgm.');
		
		
		// redirect to personalizer form
		if (isset($pObj->piVars['article_personalize_button'])) {               
			
			// Save quantities
			$pObj->updateCart();			
			$pObj->cartObj->store();

			// if there are articles to personalize or related articles, go to the personalize page
			// with the right action

			if(tx_ptgsaconfmgm_div::countArticleTypeInCart('personalize') > 0) {
				tx_pttools_div::localRedirect($pObj->pi_getPageLink($tsConf['personalizeArticlePage'],'_self', 
						array('tx_ptgsaconfmgm_controller_persarticle' => array('action' => 'personalizeArticle'))));
										
			}elseif (tx_ptgsaconfmgm_div::countArticleTypeInCart('persrelated') > 0) {
				tx_pttools_div::localRedirect($pObj->pi_getPageLink($tsConf['personalizeArticlePage'],'_self', 
						array('tx_ptgsaconfmgm_controller_persarticle' => array('action' => 'personalizeRelated'))));

			// nothing to personalize -> go to the real checkout
			} else {		
				tx_pttools_div::localRedirect($pObj->pi_getPageLink($GLOBALS['TSFE']->id,'_self', 
						array('tx_ptgsashop_pi1[persDataComplete]' => '1')));
			}

			
			
		// update cart but don't redirect
		} elseif(isset($pObj->piVars['cart_upd_submitted_no_redir'])) {
			$pObj->updateCart();
			$content .= $pObj->exec_defaultAction();
		
		// forwarded from personalizer form
		} elseif(isset($pObj->piVars['persDataComplete'])) {
			
			$cartObj = tx_ptgsashop_cart::getInstance();
			
			/**
			 * user is logged in - check if all required customer information are available
			 */
			if($GLOBALS['TSFE']->loginUser == 1) {
				
				$ret = $pObj->customerObj->getIsUserDataComplete();
				trace($ret);
				
			
				if($ret === true) {
					// all data available 
					$content .= $pObj->exec_checkout();	
				} else {

					// redirect to customer data page
					
					// set the backurl from gsa_userreg
					tx_pttools_sessionStorageAdapter::getInstance()->store('pt_gsauserreg_returnvar', 1);
					tx_pttools_sessionStorageAdapter::getInstance()->store('pt_gsauserreg_backurl', $pObj->pi_getPageLink($pObj->conf['orderPage'], '_self', array('checkOut' => '1')));
				
					tx_pttools_div::localRedirect($pObj->pi_getPageLink($pObj->conf['feUserRegPage']));
				}
				
			/**
			 * not logged in -> go to the login page
			 */
			} else {
				$content .= $pObj->exec_checkout();	
			}
			
		} else {
			$content .= $pObj->exec_defaultAction();
		}
		
		return $content;
	}
	
	
	
	/**
	 * Add support for RSA encrypted passwords
	 * 
	 * @param tx_ptgsashop_pi1 $pObj
	 * @param unknown_type $markerArray
	 */
	public function displayUserLogin_MarkerArrayHook(tx_ptgsashop_pi1 $pObj, $markerArray) {
		
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel'] == 'rsa') {
			
			require_once(t3lib_extMgm::extPath('rsaauth') . 'sv1/storage/class.tx_rsaauth_feloginhook.php');
			
			$feLoginHook = new tx_rsaauth_feloginhook();
			$result = $feLoginHook->loginFormHook();
			
			$markerArray['onSubmitFunction'] = $result[0];
			$markerArray['additionalRSAFields'] = $result[1];
			
			return $markerArray;
		}	
	}
}
?>