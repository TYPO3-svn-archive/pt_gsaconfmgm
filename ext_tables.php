<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');

foreach ($GLOBALS[$_EXTKEY . '_controllerArray'] as $prefix => $configuration) {
  
  $TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . $prefix] = 'layout,select_key,pages,recursive';
  
  // Adds an entry to the list of plugins in content elements of type "Insert plugin"
  t3lib_extMgm::addPlugin(
    array(
      'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_content.list_type' . $prefix, 
      $_EXTKEY . $prefix
    ), 
    'list_type'
  );
  
  // Include flexform
  if ($configuration['includeFlexform']) {
    $TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . $prefix] = 'pi_flexform';
    t3lib_extMgm::addPiFlexFormValue(
      $_EXTKEY . $prefix, 
      'FILE:EXT:' . $_EXTKEY . '/controller/flexform' . $prefix . '.xml'
    );
  } 
}

$TCA["tx_ptgsaconfmgm_voucher"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:pt_gsaconfmgm/locallang_db.xml:tx_ptgsaconfmgm_voucher',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptgsaconfmgm_voucher.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, code, pdfdoc_uid, order_wrapper_uid, gsa_uid, related_doc_no, related_doc_no, category_confinement, article_confinement, amount, is_percent, once_per_order, is_encashed, encashed_amount, expiry_date",
	)
);


t3lib_extMgm::addStaticFile($_EXTKEY,'typoscript/static/_default/', 'GSA ConfMgm Basics');
?>