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

require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_iApplSpecArticleDataObj.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticleCollection.php';
require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_persarticle.php';

class tx_ptgsaconfmgm_articleAsado implements tx_ptgsashop_iApplSpecArticleDataObj {
	
	/**
	 * collection of persarticle objects
	 * @var tx_ptgsaconfmgm_persarticleCollection
	 */
	protected $personArticleCollection;
	
	public function __construct() {
		$this->personArticleCollection = new tx_ptgsaconfmgm_persarticleCollection();
	}
	
	/**
	 * set article data and sanitize all values
	 * 
	 * @param $articleData
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 21.04.2009
	 */
	public function setArticleData($articleData) {	
		$this->set_personDataList($articleData);
	}
	
	public function get_personArticleCollection() {
		return $this->personArticleCollection;
	}
	
	public function set_personDataList($personDataList) {
		foreach($personDataList as $key => $personData) {
			$persarticle = new tx_ptgsaconfmgm_persarticle();
			$persarticle->setPersData($personData);
			$this->personArticleCollection->addPersArticle($persarticle);
		}
		
	}
	
    public function getDataArray(){
        $properties = array();
        foreach (get_class_vars( __CLASS__ ) as $propertyname => $pvalue) {
            $getter = 'get_'.$propertyname;
            if ($this->$getter()!=false){
                $properties[$propertyname] = $this->$getter(); // TODO: erstmal alle
            }
        }
        return $properties;
    }

    public function setDataArray($dataArray){
        foreach ($dataArray as $propertyname => $pvalue) {
            $setter = 'set_'.$propertyname;
            if (method_exists($this, $setter)){
                $this->$setter($pvalue instanceof SimpleXMLElement ? (string)$pvalue : $pvalue);
            }
        }
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
            $getter = 'get_'.$propertyname;
            if ($this->$getter()!=false){
                $dataArray[$propertyname] = $this->$getter();
            }
        }
        return serialize($dataArray);
    }


    public function setDataFromString($applSpecDataString){
        $dataArray = unserialize($applSpecDataString);
        foreach ($dataArray as $propertyname => $pvalue) {
            $setter = 'set_'.$propertyname;
            if (method_exists($this, $setter)){
                $this->$setter($pvalue);
            }
        }
    }
}


?>