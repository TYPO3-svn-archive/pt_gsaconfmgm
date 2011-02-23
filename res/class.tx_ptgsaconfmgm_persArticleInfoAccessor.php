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


require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php'; // debugging class with trace() function


class tx_ptgsaconfmgm_persArticleInfoAccessor implements tx_pttools_iSingleton {
	
	private static $uniqueInstance = NULL;
	
	
	 /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptgsaconfmgm_persArticleInfoAccessor 	unique instance of the object (Singleton) 
     * @global     
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-11-25
     */
    public static function getInstance() {
        
        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className;
        }
        return self::$uniqueInstance;
    }	
	
	/**
     * Final method to prevent object cloning (using 'clone') of the inheriting class, in order to use only the singleton unique instance of the object.
     * @param   void
     * @return  void
     * @author  Dorit Rottner <rottner@punkt.de>
     * @since   2007-05-24
     */
    public final function __clone() {

        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
        
    }
   
    
    /**
     * Get the article info-field with options to the given article id
     * 
     * @param $articleId
     * @return info array
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 12.05.2009
     */
    public function getArticleInfoEntities($articleId) {
    	$retArrr = array();
    	
    	// query preparation
        $select  = 'infotypes.uid, 
        			infotypes.title,
        			infotypes.description,
        			infotypes.inputdefault, 
        			options.uid AS ouid, 
        			options.title AS otitle, 
        			infotypes.inputtype';
        $from    = 'tx_ptconference_domain_model_persarticleinfo_types AS infotypes
					LEFT OUTER JOIN tx_ptconference_domain_model_persarticleinfo_options AS options ON options.infotype = infotypes.uid';
        $where   = 'FIND_IN_SET('.$articleId .', gsa_shop_articles) > 0';
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
 
        trace(tx_pttools_div::returnLastBuiltSelectQuery($GLOBALS['TYPO3_DB'], $select, $from, $where, $groupBy, $orderBy, $limit));
 
        
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $GLOBALS['TYPO3_DB']->sql_error());
        }
        
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        	$retArr[$row['uid']]['uid'] = $row['uid'];
        	$retArr[$row['uid']]['title'] = $row['title'];
        	$retArr[$row['uid']]['description'] = $row['description'];
        	$retArr[$row['uid']]['inputtype'] = $row['inputtype'];
        	$retArr[$row['uid']]['inputdefault'] = $row['inputdefault'];
        	$retArr[$row['uid']]['options'][$row['ouid']] = $row['otitle'];
        }
              
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $retArr;
    }
    
    /**
     * returns the label of a select value if the infotype is a selectbox
     * 
     * @return unknown_type
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 26.05.2009
     */
    public function getSelectOptionLabel($typeUid, $valueUid) {
    	// query preparation
        $select  = 'title';
        $from    = 'tx_ptconference_domain_model_persarticleinfo_options';
        $where   = sprintf("infotype = %u and uid = %u", $typeUid, $valueUid);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        trace(tx_pttools_div::returnLastBuiltSelectQuery($GLOBALS['TYPO3_DB'], $select, $from, $where, $groupBy, $orderBy, $limit));
        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $GLOBALS['TYPO3_DB']->sql_error());
        }
        
        return $res['title'];
    }
    
    
	/**
     * slect the infovalues for a persdata or relarticle 
     * 
     * @return array
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 28.05.2010
     */
    public function getInfoValues($persDataUid = 0, $relArticleUid = 0) {
    	// query preparation
        $select  = 'uid,
        			infotype,
        			infovalue,
        			persdata,
        			relarticle';
        $from    = 'tx_ptconference_domain_model_persarticleinfo_values';
        $where   = sprintf("persdata = %u and relarticle = %u", (int)$persDataUid, (int)$relArticleUid);
        $groupBy = '';
        $orderBy = '';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
        trace(tx_pttools_div::returnLastBuiltSelectQuery($GLOBALS['TYPO3_DB'], $select, $from, $where, $groupBy, $orderBy, $limit));
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        	$values[$row['uid']] = $row;	
        }

        return $values;
    }
}

?>