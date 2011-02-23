################################################################################
# Configuration of the list editDataArticleList 
# @version  $Id:$
# @author   Daniel Lienert <lienert@punkt.de>
# @since    2010-05-28
################################################################################

includeLibs.user_tx_ptgsaconfmgm_userFuncs = typo3conf/ext/pt_gsaconfmgm/res/staticlib/class.tx_ptgsaconfmgm_userFuncs.php

plugin.tx_ptlist.listConfig.persArticleDataList {

	baseFromClause (
		tx_ptgsaminidb_FSCHRIFT
		Inner Join tx_ptgsashop_order_wrappers On tx_ptgsashop_order_wrappers.related_doc_no = tx_ptgsaminidb_FSCHRIFT.AUFNR
		Inner Join tx_ptgsashop_orders On tx_ptgsashop_order_wrappers.orders_id = tx_ptgsashop_orders.uid
		Inner Join tx_ptgsashop_orders_articles articles On articles.orders_id = tx_ptgsashop_orders.uid
		INNER JOIN tx_ptconference_domain_model_persdata persdata on persdata.tx_ptgsashop_orders_articles_uid = articles.uid
		INNER JOIN tx_ptconference_domain_model_event events ON persdata.event = events.uid
	)

	baseWhereClause = TEXT
	baseWhereClause {
		cObject = COA
		cObject {	
			10 = TEXT
			10 {
				dataWrap = persdata.tx_ptgsashop_customer_uid = {TSFE:fe_user|user|tx_ptgsauserreg_gsa_adresse_id}
			}

			15 = TEXT
			15.noTrimWrap = | |
			
			20 = USER
			20 {
				userFunc = user_tx_ptgsaconfmgm_userFuncs->getSelectedEvent
			}

			30 = TEXT
			30 {
				dataWrap = and tx_ptgsaminidb_FSCHRIFT.ERFART = '04RE' And (Select Count(FSTORNO.NUMMER) From tx_ptgsaminidb_FSCHRIFT FSTORNO Where FSTORNO.ALTAUFNR = tx_ptgsaminidb_FSCHRIFT.AUFNR) = 0
			}
		}
	}
    
  baseGroupByClause (
  )

  defaults {
  }

  #structureByCols = ticketCodeColumn
  #structureByHeaders = ticketCodeColumn
  
  ############################################################################
  # General settings
  ############################################################################

  # Comma separated list of typo3 table names
  tables (
	tx_ptconference_domain_model_persdata persdata,
	tx_ptconference_domain_model_event	events,
	tx_ptgsashop_orders_articles articles
  )


  ############################################################################
  # Setting up the data descriptions
  ############################################################################

	data {
		persdataUid {
			table = persdata
			field = uid
		}
	
		eventCode {
			table = events
			field = code
		}
	
		eventStartDate {
			table = events
			field = startdate
		}
	
		eventTitle {
			table = events
			field = title
		}
	
		articleCode {
			table = articles
			field = description
		}
	
		articleUid {
			table = persdata
			field = uid
		}
	
		# ArticleCode
		ticketCode {
		  table = persdata
		  field = articlecode
		}
	
		company {
			table = persdata
			field =  company
		}

		firstname {
			table = persdata
			field =  firstname
		}

		lastname {
			table = persdata
			field =  lastname
		}	
	
		jobstatus {
			table = persdata
			field = jobstatus
		}
	}


  ############################################################################
  # Display columns configuration
  ############################################################################

	columns {	
		5 {
		  columnIdentifier = eventCodeColumn
		  dataDescriptionIdentifier = eventCode
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_eventCode
		}
		
		7 {
		  columnIdentifier = articleCodeColumn
		  dataDescriptionIdentifier = articleCode
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_articleCode
		}
		
		10 {
		  columnIdentifier = ticketCodeColumn
		  dataDescriptionIdentifier = ticketCode
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_ticketCode
		}
		
		20 {
		  columnIdentifier = companyColumn
		  dataDescriptionIdentifier = company
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_company
		}
		
		30 {
		  columnIdentifier = nameColumn
		  dataDescriptionIdentifier = lastname, firstname
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_name
		}
		
		40 {
		  columnIdentifier = jobStatusColumn
		  dataDescriptionIdentifier = jobstatus
		  label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_jobstatus
		}
		
		50 {
			columnIdentifier = editDataColumn
			dataDescriptionIdentifier = persdataUid, eventStartDate
			isSortable = 0
		  
			renderObj = COA
			renderObj {
				10 = TEXT
				10 {
					data = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_edit
					typolink {
						parameter = {$plugin.tx_ptgsaconfmgm.editPersonalDataPage}
						additionalParams.dataWrap = &tx_ptgsaconfmgm_controller_persarticle[action]=editSinglePersData&tx_ptgsaconfmgm_controller_persarticle[persDataUid]={field:persdataUid}&tx_ptgsaconfmgm_controller_persarticle[listPageUid]={page:uid}
				   }
				  
				   if.value.field = eventStartDate
				   if.isGreatherThan = SIM_EXEC_TIME
				}
			}
		}
	}

	############################################################################
	# Filters configuration
	############################################################################

    filters {
		defaultFilterbox {
			
			10 < plugin.tx_ptlist.alias.filter_options_group
			10 {
				filterIdentifier = groupEvent
				mode = select
				submitOnChange = 1
				includeEmptyOption = 1
				label = Event
				dataDescriptionIdentifier = eventTitle
			}	
		}
	}
}













plugin.tx_ptlist.listConfig.relArticleDataList {

	baseFromClause (
		tx_ptgsaminidb_FSCHRIFT
		Inner Join tx_ptgsashop_order_wrappers On tx_ptgsashop_order_wrappers.related_doc_no = tx_ptgsaminidb_FSCHRIFT.AUFNR
		Inner Join tx_ptgsashop_orders On tx_ptgsashop_order_wrappers.orders_id = tx_ptgsashop_orders.uid
		Inner Join tx_ptgsashop_orders_articles articles On articles.orders_id = tx_ptgsashop_orders.uid
		INNER JOIN tx_ptconference_domain_model_relarticle relarticle on articles.uid = relarticle.tx_ptgsashop_orders_articles_uid
		INNER JOIN tx_ptconference_domain_model_persdata persdata on persdata.uid = relarticle.persdata
		INNER JOIN tx_ptconference_domain_model_event events ON persdata.event = events.uid
	)

	baseWhereClause = TEXT
	baseWhereClause {
		cObject = COA
		cObject {

			10 = TEXT
			10 {
				dataWrap = persdata.tx_ptgsashop_customer_uid = {TSFE:fe_user|user|tx_ptgsauserreg_gsa_adresse_id}
			}
			
			15 = TEXT
			15.noTrimWrap = | |

			20 = USER
			20 {
				userFunc = tx_ptgsaconfmgm_userFuncs->getSelectedEvent
			}

			30 = TEXT
			30 {
				dataWrap = and tx_ptgsaminidb_FSCHRIFT.ERFART = '04RE' And (Select Count(FSTORNO.NUMMER) From tx_ptgsaminidb_FSCHRIFT FSTORNO Where FSTORNO.ALTAUFNR = tx_ptgsaminidb_FSCHRIFT.AUFNR) = 0
			}
		}
	}

	baseGroupByClause (
	)

	############################################################################
	# General settings
	############################################################################

	# Comma separated list of typo3 table names
	tables (
		tx_ptconference_domain_model_relarticle relarticle,
		tx_ptconference_domain_model_persdata persdata,
		tx_ptconference_domain_model_event	events,
		tx_ptgsashop_orders_articles articles
	)


	############################################################################
	# Setting up the data descriptions
	############################################################################

	data {
		relarticleUid {
			table = relarticle
			field = uid
		}

		eventCode {
			table = events
			field = code
		}

		eventStartDate {
			table = events
			field = startdate
		}

		eventTitle {
			table = events
			field = title
		}

		articleCode {
			table = articles
			field = description
		}

		articleUid {
			table = persdata
			field = uid
		}

		# ArticleCode
		ticketCode {
			table = persdata
			field = articlecode
		}

		company {
			table = persdata
			field =  company
		}

		firstname {
			table = persdata
			field =  firstname
		}

		lastname {
			table = persdata
			field =  lastname
		}	

		jobstatus {
			table = persdata
			field = jobstatus
		}

		tutorials {
			special = (SELECT group_concat(infooptions.title SEPARATOR ',') FROM tx_ptconference_domain_model_persarticleinfo_values infovalues INNER JOIN tx_ptconference_domain_model_persarticleinfo_options infooptions ON infovalues.infovalue = infooptions.uid where infovalues.relarticle = relarticle.uid group by infovalues.relarticle limit 0,1)
		}
	}


	############################################################################
	# Display columns configuration
	############################################################################

	columns {
		10 {
			columnIdentifier = eventCodeColumn
			dataDescriptionIdentifier = eventCode
			label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_eventCode
		}

		20 {
			columnIdentifier = articleCodeColumn
			dataDescriptionIdentifier = articleCode
			label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_articleCode
		}

		30 {
			columnIdentifier = tutorialColumn
			dataDescriptionIdentifier = tutorials
			label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_tutorial
		}

		40 {
			columnIdentifier = ticketCodeColumn
			dataDescriptionIdentifier = ticketCode
			label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_ticketCode
		}

		50 {
			columnIdentifier = nameColumn
			dataDescriptionIdentifier = lastname, firstname
			label = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_name
		}

		60 {
			columnIdentifier = editDataColumn
			dataDescriptionIdentifier = relarticleUid
			isSortable = 0

			renderObj = COA
			renderObj {
				10 = TEXT
				10 {
					data = LLL:EXT:pt_gsaconfmgm/locallang.xml:persdata_column_edit
					typolink {
						parameter = {$plugin.tx_ptgsaconfmgm.editPersonalDataPage}
						additionalParams.dataWrap = &tx_ptgsaconfmgm_controller_persarticle[action]=editSingleRelData&tx_ptgsaconfmgm_controller_persarticle[relDataUid]={field:relarticleUid}&tx_ptgsaconfmgm_controller_persarticle[listPageUid]={page:uid}
					}
				}
				
				if.value.field = eventStartDate
				if.isGreatherThan = SIM_EXEC_TIME
			}
		}
	}


	############################################################################
	# Filters configuration
	############################################################################

	filters {
		defaultFilterbox {
			10 < plugin.tx_ptlist.alias.filter_options_group
			10 {
				filterIdentifier = groupEvent
				mode = select
				submitOnChange = 1
				includeEmptyOption = 1
				label = Event
				dataDescriptionIdentifier = eventTitle
			}
		}	
	}
}
