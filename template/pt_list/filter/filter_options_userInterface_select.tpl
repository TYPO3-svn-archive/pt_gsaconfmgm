<form method="post" action="{url parameter=$currentPage additionalParams='&%s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.options_select'}">
	<select class="tx_ptgsaconfmgm_select" size="{$selectBoxSize}" name="{$prefixId}[value][]" {if $submitOnChange}onchange="submit()"{/if} {if $multiple}multiple{/if}>
		{foreach from=$possibleValues item=possibleValue}
			<option value="{$possibleValue.item|urlencode}"{if $possibleValue.active} selected{/if}>
				{$possibleValue.label}
			</option>
		{/foreach}
	</select>

	{if !$submitOnChange}
		<br />
		<br />
		<input type="submit" value="{$filterconf.submitValue|ll:0}" />
	{/if}
</form>