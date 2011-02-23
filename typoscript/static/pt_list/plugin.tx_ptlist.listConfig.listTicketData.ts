################################################################################
# Configuration of the list ticketData
# 
# @version  $Id:$
# @author   Daniel Lienert <lienert@punkt.de>
# @since    2009-09-08
################################################################################

#includeLibs.tx_ptgsaconfmgm = EXT:pt_gsaconfmgm/res/staticlib/class.tx_ptgsaconfmgm_userFuncs.php

plugin.tx_ptlist.listConfig.listTicketData {

  baseFromClause (
    tx_ptgsaconfmgm_user_article_rel                 AS artrel
    INNER JOIN  tx_ptgsaminidb_ARTIKEL ARTIKEL 		 ON ARTIKEL.ARTNR =  artrel.article_artnr 
    INNER JOIN  fe_users 							 ON fe_users.uid = artrel.fe_user_id
    INNER JOIN  tx_ptgsashop_order_wrappers 		 ON tx_ptgsashop_order_wrappers.uid = artrel.order_uid 
	INNER JOIN  tx_ptgsaminidb_FSCHRIFT FSCHRIFT     ON FSCHRIFT.AUFNR = tx_ptgsashop_order_wrappers.related_doc_no 
	INNER JOIN  tx_ptgsaminidb_ADRESSE ADRESSE		 ON ADRESSE.NUMMER = fe_users.tx_ptgsauserreg_gsa_adresse_id
  )



  baseWhereClause = TEXT
  baseWhereClause {
    cObject = COA
    cObject {
      10 = TEXT
      10 {
        dataWrap = artrel.article_code = {TSFE:fe_user|uc|tx_ptgsaconfmgm_checkin_ticketListWhere}
        dataWrap.intVal
      }
    }
  }
    

  baseGroupByClause (
  )

  defaults {
  }


  ############################################################################
  # General settings
  ############################################################################

  # Comma separated list of typo3 table names
  tables (
    tx_ptgsaconfmgm_user_article_rel artrel
    tx_ptgsaminidb_ARTIKEL ARTIKEL
    fe_users
	tx_ptgsashop_order_wrappers
	tx_ptgsaminidb_FSCHRIFT FSCHRIFT
	tx_ptgsaminidb_ADRESSE ADRESSE
  )


  ############################################################################
  # Setting up the data descriptions
  ############################################################################

  data {
  
    company {
      field = name
      table = ADRESSE
    }

	customerName {
      field = name
      table = fe_users
    }    
	
	billNr {
		table = FSCHRIFT
		field = AUFNR
	}
	
	outStanding {
		special =   FSCHRIFT.ENDPRB - (FSCHRIFT.BEZSUMME + FSCHRIFT.GUTSUMME)
	}
	
  }


  ############################################################################
  # Display columns configuration
  ############################################################################

  columns {

    10 {
      columnIdentifier = companyColumn
      dataDescriptionIdentifier = company
      label = 'Company'
    }
	
	20 {
      columnIdentifier = customerNameColumn
      dataDescriptionIdentifier = customerName
      label = 'Name'
    }
	
	30 {
	  columnIdentifier = billNrColumn
      dataDescriptionIdentifier = billNr
      label = 'BillNr'
	}
	
	40 {
	  columnIdentifier = outStandingColumn
      dataDescriptionIdentifier = outStanding
      label = 'Outstanding'
	}

  }
  

  ############################################################################
  # Filters configuration
  ############################################################################

  filters {
  }
}