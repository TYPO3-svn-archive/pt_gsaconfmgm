<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christoph Ehscheidt (ehscheidt@punkt.de), Daniel Lienert <lienert@punkt.de>
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

require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_iApplSpecArticleDataObj.php';

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_relarticleCollectionFactory.php';

class tx_ptgsaconfmgm_relatedAsado implements tx_ptgsashop_iApplSpecArticleDataObj {
	/**
	 * @var tx_ptgsaconfmgm_relarticleCollection
	 */
	private $relArticleCollection;
	
	private $articleId;
	
	public function __construct($articleId) {
		$this->articleId = $articleId;
		$this->relArticleCollection = tx_ptgsaconfmgm_relarticleCollectionFactory::createEmptyCollection();
	}
	
	public function getArticleId() {
		return $this->articleId;
	}
	
	public function setArticleId($id) {
		$this->articleId = $id;
	}
	

	public function addRelArticleInstance($relArticleData) {	

		$tmpRelArticle = new tx_ptgsaconfmgm_relarticle();
		$tmpRelArticle->setRelArticleData($relArticleData);
		$this->relArticleCollection->addRelArticle($tmpRelArticle);
		
	}
	
	public function getRelArticleCollection() {
		return $this->relArticleCollection;
	}
	
	/*************************************************************
     * Interface "tx_ptgsashop_iApplSpecArticleDataObj"
     *************************************************************/

    public function processOnAddItem(tx_ptgsashop_iApplSpecArticleDataObj $applSpecDataObj, $quantity){

    }

    public function processOnRemoveItem(tx_ptgsashop_iApplSpecArticleDataObj $applSpecDataObj, $quantity){

    }

    public function processOnUpdateItemQuantity($quantity){

    }


    /*************************************************************
     * Interface "tx_ptgsashop_iApplSpecDataObj"
     *************************************************************/

    public function getDataAsString(){
        foreach (get_class_vars( __CLASS__ ) as $propertyname => $pvalue) {
            $getter = 'get'.ucfirst($propertyname);
            if ($this->$getter()!=false){
                $dataArray[$propertyname] = $this->$getter();
            }
        }
        return serialize($dataArray);
    }


    public function setDataFromString($applSpecDataString){
        $dataArray = unserialize($applSpecDataString);
        foreach ($dataArray as $propertyname => $pvalue) {
            $setter = 'set'.ucfirst($propertyname);
            if (method_exists($this, $setter)){
                $this->$setter($pvalue);
            }
        }
    }
}


?>