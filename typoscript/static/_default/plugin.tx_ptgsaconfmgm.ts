plugin.tx_ptgsaconfmgm {

	personalizeArticlePage = {$plugin.tx_ptgsaconfmgm.personalizeArticlePage}
	ticketUserGroup = {$plugin.tx_ptgsaconfmgm.ticketUserGroup}
	userLoginPage = {$plugin.tx_ptgsaconfmgm.userLoginPage}
	editPersonalDataPage = {$plugin.tx_ptgsaconfmgm.editPersonalDataPage}
	
	persDataStorage = {$plugin.tx_ptgsaconfmgm.persDataStorage}
	
	articleCustomFieldWithConfig = {$plugin.tx_ptgsaconfmgm.articleCustomFieldWithConfig}
		
	view.persarticleform.template = {$plugin.tx_ptgsaconfmgm.view.persarticleform.template}
	view.persrelatedform.template = {$plugin.tx_ptgsaconfmgm.view.persrelatedform.template}
	view.editmydataform.template = {$plugin.tx_ptgsaconfmgm.view.editmydataform.template}
	
	ticketCategory = {$plugin.tx_ptgsaconfmgm.ticketCategory}
	checkInPrintoutPage = {$plugin.tx_ptgsaconfmgm.checkInPrintoutPage}
	
	ptlist.attendeeinformation.template = {$plugin.tx_ptgsaconfmgm.ptlist.attendeeinformation.template}
	
	#CSS Defaul Style
	_CSS_DEFAULT_STYLE (
		.tx_ptgsaconfmgm_fielderror {border:1px solid red; background-color: #FCBEC3;}
		.tx_ptgsaconfmgm_attendeeInfo {
			width:250px;
		}
		.tx_ptgsaconfmgm_attendeeInfo dt {
			font-weight:bold;
			float:left;
			width:100px;
		}
		
		.tx_ptgsaconfmgm_email {
			color:#777;
		}
	)
}
