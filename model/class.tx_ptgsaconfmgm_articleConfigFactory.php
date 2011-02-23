<?php
/***************************************************************
*  Copyright notice
*
*  (c)2010 Daniel Lienert (t3extensions@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_gsaconfmgm').'model/class.tx_ptgsaconfmgm_articleConfig.php'; 

class tx_ptgsaconfmgm_articleConfigFactory {
	
	static $articleConfigField = 2;
	
	static $articleEventField = 3;
	
	/**
	 * @var array of deserialised artilce Configs
	 */
	static $articleConfigCache;
	
	/**
	 * Create article config object from article data 
	 * @param $article tx_ptgsashop_baseArticle
	 * @return tx_ptgsaconfmgm_articleConfig
	 * @author Daniel Lienert <lienert@punkt.de>
	 * @since 12.05.2010
	 */
	public static function createArticleConfig(tx_ptgsashop_baseArticle $article = NULL) {
		
		$articleConfig = new tx_ptgsaconfmgm_articleConfig();

		if(is_object($article)) {
			
			if(is_object(self::$articleConfigCache[$article->get_id()])) return self::$articleConfigCache[$article->get_id()];
			
			$getter = 'get_userField' . sprintf("%02s",self::$articleConfigField);
			$configSerialized = $article->$getter();
			
			if(strlen($configSerialized) != 0) $articleConfig->setSerializedConfig($configSerialized);
				
			$getter = 'get_userField' . sprintf("%02s",self::$articleEventField);
			$articleConfig->setEventUid((int) $article->$getter());
		}
		
		self::$articleConfigCache[$article->get_id()] = $articleConfig;
		return $articleConfig;
	}		
}
?>