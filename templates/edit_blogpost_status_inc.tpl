{if $gBitSystem->isFeatureActive( 'liberty_display_status' ) && $gBitSystem->isFeatureActive( 'liberty_display_status_menu' ) && ($gBitUser->hasPermission('p_liberty_edit_content_status') || $gBitUser->hasPermission('p_liberty_edit_all_status'))}
	<div class="row">
		{formlabel label="Publish Status" for="content_status_id"}
		{forminput}
			{html_options name="content_status_id" options=$gContent->getAvailableContentStatuses() selected=$gContent->getField('content_status_id',$smarty.const.BIT_CONTENT_DEFAULT_STATUS)}
		{/forminput}
		{formhelp note="Select Public to publish your story. To automatically publish your story in the future, set the Publish Status to Public and set the publish date ahead. Click the Advanced tab above to set the publish data."}
	</div>
{/if}
