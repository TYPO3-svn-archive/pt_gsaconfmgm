<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$TCA["tx_ptgsaconfmgm_voucher"] = array (
	"ctrl" => $TCA["tx_ptgsaconfmgm_voucher"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden, code, pdfdoc_uid, order_wrapper_uid, gsa_uid, related_doc_no, related_doc_no, category_confinement, article_confinement, amount, is_percent, once_per_order, is_encashed, encashed_amount, expiry_date"
	),
	"feInterface" => $TCA["tx_ptgsaconfmgm_voucher"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"code" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.code",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => "required,trim",
			)
		),
		"pdfdoc_uid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.pdfdoc_uid",		
            "config" => Array (
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'int,nospace',
                "default" => 0
			)
		),
        "order_wrapper_uid" => Array (      
            "exclude" => 1,     
            "label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.order_wrapper_uid",      
            "config" => Array (
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'int,nospace',
                "default" => 0
            )
        ),
		"gsa_uid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.gsa_uid",		
            "config" => Array (
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'int,nospace',
                "default" => 0
            )
		),
		"related_doc_no" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.related_doc_no",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => "trim",
			)
		),
		"category_confinement" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.category_confinement",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => "trim",
			)
		),
		"article_confinement" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.article_confinement",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "100",	
				"eval" => "trim",
			)
		),
		"amount" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.amount",		
            'config' => array(
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'double2',
            )
		),
		"is_percent" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.is_percent",		
			"config" => Array (
				"type" => "check",
			)
		),
        "once_per_order" => Array (     
            "exclude" => 1,     
            "label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.once_per_order",     
            "config" => Array (
                "type" => "check",
            )
        ),
        "is_encashed" => Array (     
            "exclude" => 1,     
            "label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.is_encashed",     
            "config" => Array (
                "type" => "check",
            )
        ),
        "encashed_amount" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.encashed_amount",		
            'config' => array(
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'double2',
            )
		),
		"expiry_date" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:pt_gsavoucher/locallang_db.xml:tx_ptgsaconfmgm_voucher.expiry_date",		
            "config" => Array (
                "type" => "input",  
                "size" => "30", 
                "max" => "10",  
                "eval" => "trim",
            )
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, type, code, pdfdoc_uid, order_wrapper_uid, gsa_uid, related_doc_no, related_doc_no, category_confinement, article_confinement, amount, is_percent, once_per_order, is_encashed, encashed_amount, expiry_date")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>