<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christoph Ehscheidt (t3extensions@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_abstractArticle.php';

class tx_ptgsaconfmgm_relarticle  extends tx_ptgsaconfmgm_abstractArticle {
	
	/*
	 * @var int Reference to the selected persarticle
	 */
	protected $ppersArticleReference;
	
	public function __construct($id = 0) {
		$this->tableName = 'tx_ptconference_domain_model_relarticle';
		parent::__construct($id);
		
		if($id){
			$this->infoValueCollection = tx_ptgsaconfmgm_infoValueCollectionFactory::createFromDataBaseByRelArticleUid($this->get_persdata(),$id);
			$this->rowUid = $id;
		}
	}
	
	public function setArticleUid($articleUid) {
		$this->set_tx_ptgsashop_orders_articles_uid($articleUid);
	}
	
	public function setPersArticleUid($articleUid) {
		$this->set_persdata($articleUid);
	}
	
	public function setRelArticleData($relArticleData) {
		
		$this->set_pid($this->getStoragePID());
		
		$this->persArticleReference = $relArticleData['relatedArticle'];
		
		$temp = explode('.', $this->persArticleReference);
		if((int) $temp[1]) $this->set_persdata((int) $temp[1]);
		
		$this->setInfoValuesFromArray($relArticleData['info']);
	}
	
	
	public function getRelArticleData() {
		$relArticleData['relatedArticle'] = $this->persArticleReference;
		$relArticleData['info'] = $this->getInfoValuesAsArray();
		return $relArticleData;
	}
	
	public function getPersArticleReference() {
		return $this->persArticleReference;
	}
	
	/*
	 * get the storage pid from ts_config
	 */
	protected function getStoragePID() {
		$tsConf = tx_pttools_div::typoscriptRegistry('plugin.tx_ptgsaconfmgm.');
		$persDataStorage = $tsConf['persDataStorage'];
		return $persDataStorage;
	}
	
	
	protected function setAvailableFields() {
		
		$this->availableFieldsArray = array(
			'uid',
			'pid',
			'tstamp',
			'crdate',
			'cruser_id',
			'persdata',
			'tx_ptgsashop_orders_articles_uid',
			'tx_ptgsashop_customer_uid',
			'articlecode',
		);
	}
}

?>