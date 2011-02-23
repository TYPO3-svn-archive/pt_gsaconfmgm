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

require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_orderProcessor.php';// GSA shop order processor class

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticle.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticleCollection.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_articleConfigFactory.php';

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_articleAsado.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_relatedAsado.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/staticlib/class.tx_ptgsaconfmgm_div.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'res/class.tx_ptgsaconfmgm_voucher.php';


class tx_ptgsaconfmgm_hooks_PostOrderProcessing  {

	/**
	 * @param $params array
	 * @param $procObj tx_ptgsashop_orderProcessor
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 22.04.2009
	 */
	public function processUserData($params, tx_ptgsashop_orderProcessor $procObj) {

		$articleCollections = array();
		
		foreach($params['orderWrapperObj']->get_orderObj()->getCompleteArticleCollection() as $artKey => $artObj) {
			$asado = $artObj->get_applSpecDataObj();
			
			if(is_object($asado)) {

				if($asado instanceof tx_ptgsaconfmgm_articleAsado) {
					
					$this->processPersonalizedArticle($artObj);
					$articleCollections[] = $asado->get_personArticleCollection();
				} 
			}
		}
		
		foreach($params['orderWrapperObj']->get_orderObj()->getCompleteArticleCollection() as $artKey => $artObj) {
			$asado = $artObj->get_applSpecDataObj();
			if(is_object($asado)) {
		
				if($asado instanceof tx_ptgsaconfmgm_relatedAsado) {
				
					$this->processRelatedArticle($artObj,$articleCollections);
					
				} 
			}
		}
	}

	/**
	 * Proccess the personalized articles
	 * 
	 * @param tx_ptgsashop_baseArticle $article
	 * @return void
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	protected function processPersonalizedArticle(tx_ptgsashop_baseArticle $article) {
		
		foreach($article->get_applSpecDataObj()->get_personArticleCollection() as $persKey => $persArticle) {

			// if the article requires a voucher, encash it
			$artConf = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($article);
			if($artConf->isVoucherRequired()) {
				$this->encashVoucher($persArticle->getVoucherCode(), $article);
			}

			// save the new personalized article		
			$persArticle->setArticleUid($article->get_orderArchiveUid());
			$persArticle->set_event($artConf->getEventUid());
			$persArticle->set_tx_ptgsashop_customer_uid(tx_ptgsashop_sessionFeCustomer::getInstance()->get_gsaCustomerObj()->get_gsauid());
			$persArticle->save();


		}
	}

	/**
	 * Save the related articles to the database
	 * 
	 * @param $article tx_ptgsashop_baseArticle an article with related flag
	 * @param $persArticles array list of persarticle collections
	 * @return void
	 * @since 19.05.2010
	 */
	protected function processRelatedArticle(tx_ptgsashop_baseArticle $article, array $persArticles) {
			
		foreach($article->get_applSpecDataObj()->getRelArticleCollection() as $relArticle) {

			if((int) $relArticle->get_persdata() == 0) {
				foreach($persArticles as $persCollection) {	
					if($persCollection->hasItem($relArticle->getPersArticleReference())) {
						$persId = $persCollection->getItemById($relArticle->getPersArticleReference())->getRowUid();
						$relArticle->set_persdata($persId);
						break;
					}
				}	
			} 
					
			$relArticle->set_tx_ptgsashop_orders_articles_uid($article->get_orderArchiveUid());
			$relArticle->set_tx_ptgsashop_customer_uid(tx_ptgsashop_sessionFeCustomer::getInstance()->get_gsaCustomerObj()->get_gsauid());
			$relArticle->save();
			
		}
	}


	/**
	 * Encash the voucher with the given code
	 *
	 * @param $voucherCode
	 * @param $article tx_ptgsashop_baseArticle 
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 22.06.2009
	 */
	protected function encashVoucher($voucherCode, tx_ptgsashop_baseArticle $article) {
		$voucher = new tx_ptgsaconfmgm_voucher(0, $voucherCode);

		tx_pttools_assert::isValidUid($voucher->get_uid(), false);

		$voucher->set_gsaUid($article->get_customerId());
		$voucher->set_orderArticleUid($article->get_orderArchiveUid());
				
		$voucher->encashAmount(100);
		$voucher->set_isEncashed(true);
		$voucher->storeSelf();
	}
}

?>