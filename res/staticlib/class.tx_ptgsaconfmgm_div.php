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

require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';

require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_cart.php';  // GSA shop cart class

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_articleConfigFactory.php'; 


class tx_ptgsaconfmgm_div {
	
		
	/**
	 * Get the amount of articles of the given type in the cart
	 * 
	 * @param $articleType
	 * @return int amount of articles of the given type
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 19.05.2009
	 */
	public static function countArticleTypeInCart($articleType) {
		$articleType = strtolower($articleType);
		
		tx_pttools_assert::isInArray($articleType, array('personalize', 'persrelated'));
				
		$cartObj = tx_ptgsashop_cart::getInstance();
		$count = 0;
		
		foreach($cartObj as $key => $article) {
			$articleConfig = tx_ptgsaconfmgm_articleConfigFactory::createArticleConfig($article);
			if($articleType == 'personalize' && $articleConfig->isPersonalizable()) $count++;
			if($articleType == 'persrelated' && $articleConfig->isRelated()) $count++;
		}
		
		return $count;
	}
	
	
}
?>