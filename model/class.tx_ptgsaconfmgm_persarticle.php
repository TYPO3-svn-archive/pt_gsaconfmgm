<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christoph Ehscheidt, Daniel Lienert (t3extensions@punkt.de)
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

class tx_ptgsaconfmgm_persarticle extends tx_ptgsaconfmgm_abstractArticle {
	
	private $voucherCode;
	private $gsaCustomerUid;
	
	public function __construct($id = 0) {
		$this->tableName = 'tx_ptconference_domain_model_persdata';
		parent::__construct($id);
		
		if($id){
			$this->infoValueCollection = tx_ptgsaconfmgm_infoValueCollectionFactory::createFromDataBaseByPersDataUid($id);
			$this->rowUid = $id;
		}
	}
	
	/**
	 * set data and infoValues
	 * 
	 * @param $persArticleData array of FormData
	 * @return void
	 * @author Daniel Lienert <daniel@lienert.cc>
	 */
	public function setPersData($persArticleData) {
		
		$this->set_pid($this->getStoragePID());
		
		$this->set_company($persArticleData['firm']);
		$this->set_title($persArticleData['title']);
		$this->set_firstname($persArticleData['firstname']);
		$this->set_middlename($persArticleData['middlename']);
		$this->set_lastname($persArticleData['lastname']);
		$this->set_email($persArticleData['email']);
		$this->set_jobstatus($persArticleData['jobstatus']);

		$this->set_currenthash($this->generateHash());
		
		$this->setVoucherCode($persArticleData['vouchercode']);

		$this->setInfoValuesFromArray($persArticleData['info']);
	}
	
	
	/**
	 * Generate a hashsum over printed fields
	 */
	protected function generateHash() {
		$relevantData = array('company','country','firstname','lastname','jobstatus');
		foreach($relevantData as $relevantField) {
			$getterMethod = 'get_'. ucfirst($relevantField);
			$badgeDataString = '';
			
			if(method_exists($this, $getterMethod)) {
				$badgeDataString .= $this->$getterMethod();
			}
		}
		
		return md5($badgeDataString);
	}
	
	
	/**
	 * get data as array
	 * 
	 * @return array persData
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 28.05.2010
	 */
	public function getPersData(){ 
		$persArticleData = array(
			'firm' => $this->get_company(),
			'title' => $this->get_title(),
			'firstname' => $this->get_firstname(),
			'middlename' => $this->get_middlename(),
			'lastname' => $this->get_lastname(),
			'email' => $this->get_email(),
			'jobstatus' => $this->get_jobstatus(),
			//'vouchercode' => $this->get_vouchercode(),
			'info' => $this->getInfoValuesAsArray(),
		);
				
		return $persArticleData;
	}
	
	public function setArticleUid($articleUid) {
		$this->set_tx_ptgsashop_orders_articles_uid($articleUid);
	}
	
	public function setVoucherCode($code) {
		$this->voucherCode = $code;
	}
	
	public function getVoucherCode() {
		return $this->voucherCode;
	}
	
	/**
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
			'event',
			'company',
			'title',
			'firstname',
			'middlename',
			'lastname',
			'email',			
			'jobstatus',
			'articlecode',
			'tx_ptgsashop_orders_articles_uid',
			'tx_ptgsashop_customer_uid',
			'checkedin',
			'goodiesreceived',
			'currenthash',
		);
	}
}
?>