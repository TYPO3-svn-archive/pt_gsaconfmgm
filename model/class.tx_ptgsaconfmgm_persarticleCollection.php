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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';

class tx_ptgsaconfmgm_persarticleCollection extends tx_pttools_objectCollection {
	
	public function addPersArticle(tx_ptgsaconfmgm_persarticle $persArticle) {
		$this->addItem($persArticle, $this->generatePersArticleId($persArticle));
	}
	
	protected function generatePersArticleId(tx_ptgsaconfmgm_persarticle $persArticle) {
		$hash = spl_object_hash($persArticle);
		if($persArticle->get_uid()) $hash .= '.' . $persArticle->get_uid();
		return $hash;
	}
	
	/**
	 * get the spl object hash by dataset uid
	 * 
	 * @param $dsUID int
	 * @return objectHash
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 08.06.2010
	 */
	public function getObjectHashByDSID($dsUID){
		foreach($this->itemsArr as $key => $item) {
			if($item['uid'] == $dsUID) return $key;
		}
	}
	
}
?>