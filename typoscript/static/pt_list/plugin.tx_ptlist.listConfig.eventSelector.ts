################################################################################
# Event Selector
# @version  $Id:$
# @author   Daniel Lienert <lienert@punkt.de>
# @since    2010-05-28
################################################################################


plugin.tx_ptlist.listConfig.eventSelector {

	baseFromClause (
		tx_ptconference_domain_model_event	events
	)

	baseWhereClause (
	)

	baseGroupByClause (
	)

	defaults {
	}


	############################################################################
	# General settings
	############################################################################

	# Comma separated list of typo3 table names
	tables (
		tx_ptconference_domain_model_event	events
	)


	############################################################################
	# Setting up the data descriptions
	############################################################################

	data {

		eventCode {
			table = events
			field = code
		}

		eventTitle {
			table = events
			field = title
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

				hideResetLink = 1
			}	
		}
	}
}