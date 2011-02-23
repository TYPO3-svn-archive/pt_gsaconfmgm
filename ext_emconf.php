<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_gsaconfmgm"
#
# Auto generated 10-09-2009 15:30
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'GSA Conference Management',
	'description' => 'Extends the GSA Shop with Conference management functions',
	'category' => 'General Shop Applications',
	'author' => 'Daniel Lienert',
	'author_email' => 'lienert@punkt.de',
	'shy' => '',
	'dependencies' => 'pt_gsashop,pt_tools,pt_gsauserreg,pt_objectstorage,pt_list',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'punkt.de GmbH',
	'version' => '0.2.3',
	'constraints' => array(
		'depends' => array(
			'pt_gsashop' => '',
			'pt_tools' => '',
			'pt_gsauserreg' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:55:{s:9:"ChangeLog";s:4:"a8fc";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"5837";s:17:"ext_localconf.php";s:4:"1c73";s:14:"ext_tables.php";s:4:"bca9";s:14:"ext_tables.sql";s:4:"168d";s:13:"locallang.xml";s:4:"d8cd";s:16:"locallang_db.xml";s:4:"3b68";s:7:"tca.php";s:4:"d2e2";s:55:"controller/class.tx_ptgsaconfmgm_controller_checkin.php";s:4:"07ee";s:58:"controller/class.tx_ptgsaconfmgm_controller_editmydata.php";s:4:"3af8";s:59:"controller/class.tx_ptgsaconfmgm_controller_persarticle.php";s:4:"c7ff";s:19:"doc/Steuercodes.doc";s:4:"0c34";s:19:"doc/wizard_form.dat";s:4:"acf7";s:20:"doc/wizard_form.html";s:4:"09f3";s:50:"view/class.tx_ptgsaconfmgm_view_editmydataform.php";s:4:"8930";s:51:"view/class.tx_ptgsaconfmgm_view_persarticleform.php";s:4:"a961";s:51:"view/class.tx_ptgsaconfmgm_view_persrelatedform.php";s:4:"b565";s:52:"view/class.tx_ptgsaconfmgm_view_registerbarcodes.php";s:4:"1707";s:63:"typoscript/static/plugin.tx_ptlist.listConfig.listTicketData.ts";s:4:"a134";s:36:"typoscript/static/_default/config.ts";s:4:"55d1";s:40:"typoscript/static/_default/constants.txt";s:4:"871d";s:52:"typoscript/static/_default/plugin.tx_ptgsaconfmgm.ts";s:4:"9a75";s:36:"typoscript/static/_default/setup.txt";s:4:"51c0";s:43:"res/class.tx_ptgsaconfmgm_article_asado.php";s:4:"35d9";s:53:"res/class.tx_ptgsaconfmgm_persArticleInfoAccessor.php";s:4:"e55d";s:46:"res/class.tx_ptgsaconfmgm_persArticle_User.php";s:4:"70f2";s:53:"res/class.tx_ptgsaconfmgm_persArticle_UserArticle.php";s:4:"d387";s:63:"res/class.tx_ptgsaconfmgm_persArticle_UserArticleCollection.php";s:4:"3fdc";s:56:"res/class.tx_ptgsaconfmgm_persArticle_UserCollection.php";s:4:"ee33";s:50:"res/class.tx_ptgsaconfmgm_persArticle_UserMail.php";s:4:"7d4b";s:34:"res/class.tx_ptgsaconfmgm_user.php";s:4:"49bf";s:42:"res/class.tx_ptgsaconfmgm_userAccessor.php";s:4:"6a33";s:37:"res/class.tx_ptgsaconfmgm_voucher.php";s:4:"b293";s:45:"res/class.tx_ptgsaconfmgm_voucherAccessor.php";s:4:"fe6e";s:43:"res/staticlib/class.tx_ptgsaconfmgm_div.php";s:4:"f31a";s:49:"res/staticlib/class.tx_ptgsaconfmgm_userFuncs.php";s:4:"9053";s:72:"res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_PostOrderProcessing.php";s:4:"8b7d";s:74:"res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_ptgsashop_basearticle.php";s:4:"442f";s:66:"res/pt_gsashop_hooks/class.tx_ptgsaconfmgm_hooks_ptgsashop_pi1.php";s:4:"db8f";s:72:"res/pt_gsauserreg_hooks/class.tx_ptgsaconfmgm_hooks_ptgsauserreg_pi2.php";s:4:"c828";s:73:"res/pt_gsauserreg_hooks/class.tx_ptgsaconfmgm_hooks_ptgsauserreg_user.php";s:4:"3509";s:81:"res/pt_gsauserreg_hooks/class.tx_ptgsaconfmgm_hooks_ptgsauserreg_userAccessor.php";s:4:"0f1a";s:27:"template/editmydataform.tpl";s:4:"38d0";s:28:"template/persarticleform.tpl";s:4:"c5bb";s:28:"template/persrelatedform.tpl";s:4:"8db0";s:29:"template/registerbarcodes.tpl";s:4:"48f8";s:50:"template/mail/mail_infoTicketPurchased_default.tpl";s:4:"2b66";s:39:"model/class.tx_ptgsaconfmgm_checkin.php";s:4:"0371";s:47:"model/class.tx_ptgsaconfmgm_editarticleinfo.php";s:4:"78da";s:43:"model/class.tx_ptgsaconfmgm_persarticle.php";s:4:"16be";s:43:"model/class.tx_ptgsaconfmgm_persrelated.php";s:4:"2905";s:41:"model/class.tx_ptgsaconfmgm_stocklist.php";s:4:"6ab8";s:38:"model/class.tx_ptgsaconfmgm_ticket.php";s:4:"436e";s:48:"model/class.tx_ptgsaconfmgm_ticketCollection.php";s:4:"32c0";}',
	'suggests' => array(
	),
);

?>