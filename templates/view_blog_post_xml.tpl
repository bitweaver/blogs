{if $smarty.request.format !== 'full'}
	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$post_info}<br/>
	{* this bit is a hack to account for how parser treats splits *}
	{if $smarty.request.format == 'more'}<br/>{/if}
	{$parsed_data}
{else}
	{include file="bitpackage:blogs/view_blog_post.tpl"}
{/if}
