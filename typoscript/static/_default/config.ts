config.pt_gsaconfmgm.smarty {
	caching = 0
	debugging = 1
	t3_languageFile = EXT:pt_gsaconfmgm/locallang.xml
}

config.pt_mail.mailId.pt_gsaconfmgm_infoTicketPurchased {
    body = TEMPLATE
    body {
        template.file = EXT:pt_gsaconfmgm/template/mail/mail_infoTicketPurchased_%s.tpl
        template = FILE
        workOnSubpart = BODY
    }
}
config.pt_gsapdfdocs {
	xmlSmartyTemplate = EXT:pt_gsaconfmgm/template/invoice.xml
}

page.includeCSS {
	file27 = EXT:pt_gsaconfmgm/res/css/t_gsaconfmgm.css
}