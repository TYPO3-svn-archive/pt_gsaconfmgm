################################################################################
# Standard PersoInfo Table (Has to be defined for specific statistics)
# @version  $Id:$
# @author   Daniel Lienert <lienert@punkt.de>
# @since    2010-05-28
################################################################################

includeLibs.tx_ptgsaconfmgm_userFuncs = EXT:pt_gsaconfmgm/res/staticlib/class.tx_ptgsaconfmgm_userFuncs.php

plugin.tx_ptlist.listConfig.persInfoBase  {

  baseFromClause (
    tx_ptgsaminidb_FSCHRIFT
    Inner Join tx_ptgsashop_order_wrappers orderwrappers On orderwrappers.related_doc_no = tx_ptgsaminidb_FSCHRIFT.AUFNR
    Inner Join tx_ptgsashop_orders orders On orderwrappers.orders_id = orders.uid
    Inner Join tx_ptgsashop_orders_articles articles On articles.orders_id = orders.uid
	
	INNER JOIN tx_ptgsaconfmgm_persdata persdata on persdata.tx_ptgsashop_orders_articles_uid = articles.uid
	INNER JOIN tx_ptgsaconfmgm_events events ON persdata.event_uid = events.uid
	INNER JOIN tx_ptgsaconfmgm_persarticleinfo_values persvalues ON persvalues.tx_ptgsaconfmgm_persdata_uid = persdata.uid
	INNER JOIN tx_ptgsaconfmgm_persarticleinfo_types infotypes ON persvalues.infotype_uid = infotypes.uid
	LEFT OUTER JOIN tx_ptgsaconfmgm_persarticleinfo_options infooptions on persvalues.infovalue = infooptions.uid
  )

  baseWhereClause = TEXT
  baseWhereClause {
	cObject = COA
    cObject {
	  10 = TEXT
	  10 {
		dataWrap = tx_ptgsaminidb_FSCHRIFT.ERFART = '04RE' And (Select Count(FSTORNO.NUMMER) From tx_ptgsaminidb_FSCHRIFT FSTORNO Where FSTORNO.ALTAUFNR = tx_ptgsaminidb_FSCHRIFT.AUFNR) = 0
	  }
	  
	  20 = USER
	  20 {
		userFunc = tx_ptgsaconfmgm_userFuncs->getSelectedEvent
	  }
	  
    }
  }
  
  baseGroupByClause (

  )
  
  tables (
   
  )
  
  data {
	infovalue {
      table = persvalues
	  field = infovalue
    }
	
	infotitle {
		table = infotypes
		field = title
    }
	
	infooptionstitle {
		table = infooptions
		field = title
	}
	
  }


  columns {

	10 {
      columnIdentifier = infoTypeColumn
      dataDescriptionIdentifier = infotitle 
      label = 'InfoTitle'
    }
  
	20 {
      columnIdentifier = infoValueColumn
      dataDescriptionIdentifier = infovalue 
      label = 'InfoValue'
    }
	
	30 {
      columnIdentifier = infoOptionTitleColumn
      dataDescriptionIdentifier = infooptionstitle 
      label = 'InfoOptionsValue'
    }

  }
  
  filters {
  }
}