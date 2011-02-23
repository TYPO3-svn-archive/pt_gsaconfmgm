<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert (t3extensions@punkt.de)
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

class tx_ptgsaconfmgm_persArticleAccessor implements tx_pttools_iSingleton {

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
     * select all users by event and current customer
     * 
     * @param $eventId
     * @return unknown_type
     * @author Daniel Lienert <lienert@punkt.de>
     * @since 02.06.2010
     */
	public function selectAllByEvent($eventId, $customerUid) {
		tx_pttools_assert::isPositiveInteger($eventId,false);		
		
		// select the user choices
        $select  = 'persdata.*';
        $from    = 'tx_ptconference_domain_model_persdata persdata';
        $where   = 'tx_ptgsashop_customer_uid = ' . (int) $customerUid;
        $groupBy = '';
        $orderBy = 'lastname, firstname';
        $limit   = '';
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

        trace(tx_pttools_div::returnLastBuiltSelectQuery($GLOBALS['TYPO3_DB'], $select, $from, $where, $groupBy, $orderBy, $limit));

        if ($res == false) {
            throw new tx_pttools_exception('Query failed', 1, $GLOBALS['TYPO3_DB']->sql_error());
        }
        
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        	$retArr[$row['uid']] = $row;      	
        }
              
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        return $retArr;
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
}
?>