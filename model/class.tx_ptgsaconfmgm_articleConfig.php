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

class tx_ptgsaconfmgm_articleConfig {
	
	/**
	 * @var int uid of the event
	 */
	protected $eventUid;
	
	/**
	 * @var boolean is it a physical article (needed for delivery fees)
	 */
	protected $physical;
	
	/**
	 * @var boolean is it personalizable
	 */
	protected $personalize;
	
	/**
	 * @var boolean is it related to another personalized article
	 */
	protected $persrelated;
	
	/**
	 * @var is a voucher required for this article
	 */
	protected $voucherRequired;
	
	public function getEventUid() {
		return $this->eventUid;
	}
	
	public function isPhysical() {
		return $this->physical;
	}
	
	public function isPersonalizable() {
		return $this->personalize;
	}
	
	public function isRelated() {
		return $this->persrelated;
	}
	
	public function isVoucherRequired() {
		return $this->voucherRequired;
	}
	
	public function setVoucherRequired($voucherRequired) {
		$this->voucherRequired = $voucherRequired;
	}
	
	
	public function setEventUid($eventUid) {
		$this->eventUid = (int) $eventUid;
	}
	
	public function setPhysical($physical) {
		$this->physical = (bool) $physical;
	}
	
	public function setPersonalizable($personalizable) {
		$this->personalize = (bool) $personalizable;
	}
	
	public function setRelated($related) {
		$this->persrelated = (bool) $related;
	}
	
	public function getSerializedConfig() {
		
		$configArray['personalizable'] = $this->isPersonalizable();
		$configArray['related'] = $this->isRelated();
		$configArray['physical'] = $this->isPhysical();
		$configArray['voucherrequired'] = $this->isVoucherRequired();
		
		return serialize($configArray);
	}
	
	public function setSerializedConfig($serializedConfig) {
		$configArray = unserialize($serializedConfig);
				
		$this->setPersonalizable($configArray['personalizable']);
		$this->setRelated($configArray['related']);
		$this->setPhysical($configArray['physical']);
		$this->setVoucherRequired($configArray['voucherrequired']);
	}
}
?>