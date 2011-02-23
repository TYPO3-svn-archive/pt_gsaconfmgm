<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_ptgsaconfmgm_persarticleinfo_types=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_ptgsaconfmgm_persarticleinfo_options=1');


$GLOBALS[$_EXTKEY . '_controllerArray'] = array(
  '_controller_persarticle' => array('includeFlexform' => false),
  '_controller_checkin' => array('includeFlexform' => false),
  '_controller_editmydata' => array('includeFlexform' => false),
);

$cN = t3lib_extMgm::getCN($_EXTKEY);

foreach (array_keys($GLOBALS[$_EXTKEY . '_controllerArray']) as $prefix) {
  $path = t3lib_div::trimExplode('_', $prefix, 1);
  $path = implode('/', array_slice($path, 0, -1)); // remove class name from the end
  // Add PlugIn to Static Template #43
  t3lib_extMgm::addPItoST43($_EXTKEY, $path . '/class.' . $cN . $prefix . '.php', $prefix, 'list_type', 0);
}


if (TYPO3_MODE == 'FE') { // WARNING: do not remove this condition since this may stop the backend from working!
    
	/*
	 * Frontend Hooks
	 */
	
	/*
	 * Hooks for pt_gsashop
	 */
    require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_ptgsashop_pi1.php');
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi1_hooks']['displayShoppingCart_MarkerArrayHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsashop_pi1';
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi1_hooks']['mainControllerHook'] = 'tx_ptgsaconfmgm_hooks_ptgsashop_pi1';
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['pi1_hooks']['displayUserLogin_MarkerArrayHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsashop_pi1';
    
    require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_PostOrderProcessing.php');
    $TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['orderProcessor_hooks']['postOrderProcessingHook'][] = 'tx_ptgsaconfmgm_hooks_PostOrderProcessing->processUserData';
    
    require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_ptgsashop_basearticle.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsashop']['article_hooks']['constructor_additionalActionHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsashop_basearticle';
	
	
	/*
	 * Hooks for pt_gsauserreg
	 */
	require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsauserreg_hooks/class.tx_ptgsaconfmgm_hooks_ptgsauserreg_pi2.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsauserreg']['pi2_hooks']['main_formdescHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsauserreg_pi2';
	
	require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsauserreg_hooks/class.tx_ptgsaconfmgm_hooks_ptgsauserreg_user.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsauserreg']['user_hooks']['simulateGetterSetterHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsauserreg_user';
	
	
	/*
	 * Hooks for pt_gsapdfdocs
	 */
	require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsapdfdocs_hooks/class.tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsapdfdocs']['tx_ptgsapdfdocs_invoice']['markerArrayHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice';
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsapdfdocs']['tx_ptgsapdfdocs_controller_download']['checkAccessAllowedHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice';

} else {
	
	/*
	 * Backend Hooks
	 */
	require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsaadmin_hooks/class.tx_ptgsaconfmgm_hooks_ptgsaadmin_module2.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsaadmin']['module2_hooks']['returnArticleForm_completeForm'][] = 'tx_ptgsaconfmgm_hooks_ptgsaadmin_module2->returnArticleForm_completeForm';
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsaadmin']['module2_hooks']['createArticleFromFormData_processData'][] = 'tx_ptgsaconfmgm_hooks_ptgsaadmin_module2->createArticleFromFormData_processData';
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsaadmin']['module2_hooks']['loadArticleDefaults'][] = 'tx_ptgsaconfmgm_hooks_ptgsaadmin_module2->loadArticleDefaults';
	
	/*
	 * Hooks for pt_gsapdfdocs
	 */
	require_once(t3lib_extMgm::extPath('pt_gsaconfmgm').'res/pt_gsapdfdocs_hooks/class.tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice.php');
	$TYPO3_CONF_VARS['EXTCONF']['pt_gsapdfdocs']['tx_ptgsapdfdocs_invoice']['markerArrayHook'][] = 'tx_ptgsaconfmgm_hooks_ptgsapdfdocs_invoice';
}
?>