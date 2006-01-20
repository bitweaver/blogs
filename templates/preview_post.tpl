<h2>{tr}Preview{/tr}: {$page}</h2>
<div class="posthead">
{if $blog_data.use_title eq 'y'}
	{$title}<br />
	<small>{tr}posted by{/tr} {displayname hash=$gBitUser->mInfo} on {$created|bit_short_datetime}</small>
{else}
	{$created|bit_short_datetime}<small>{tr}posted by{/tr} {displayname hash=$gBitUser}</small>
{/if}
</div>
<div class="postbody">
{$parsed_data}
</div>
