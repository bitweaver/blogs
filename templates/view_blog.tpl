{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/view_blog.tpl,v 1.1 2005/06/19 03:57:42 bitweaver Exp $ *}
<div class="display blogs">
<div class="floaticon">
	{if $gBitUser->hasPermission( 'bit_p_blog_post' )}
		{if ($gBitUser->mUserId and $creator eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' ) or $public eq 'y' or $bit_p_blog_post eq 'y'}
			<a title="{tr}post{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}post.php?blog_id={$blog_id}">{biticon ipackage=liberty iname="post" iexplain="post"}</a>
		{/if}
	{/if}

	{if $gBitSystemPrefs.package_rss eq 'y' && $rss_blog eq 'y'}
		<a title="{tr}RSS feed{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}blog_rss.php?blog_id={$blog_id}">{biticon ipackage="rss" iname="rss" iexplain="RSS feed"}</a>
	{/if}

	{if ($gBitUser->mUserId and $creator eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' )}
		<a title="{tr}Edit blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}edit.php?blog_id={$blog_id}">{biticon ipackage=liberty iname="config" iexplain="edit"}</a>
	{/if}
	
	{if $gBitUser->isRegistered() and $gBitSystemPrefs.feature_user_watches eq 'y'}
		{if $user_watching_blog eq 'n'}
			<a title="{tr}monitor this blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=add">{biticon ipackage="users" iname="watch" iexplain="monitor this blog"}</a>
		{else}
			<a title="{tr}stop monitoring this blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=remove">{biticon ipackage="users" iname="unwatch" iexplain="stop monitoring this blog"}</a>
		{/if}
	{/if}
</div>

<div class="header">
    <h1>{tr}Blog{/tr}: {$title}</h1>
    {if $description}<h2>{tr}Description:{/tr} {$description}</h2>{/if}
{if strlen($heading) > 0}
    <div class="introduction">{eval var=$heading}</div>
{else}
    <div class="date">
        {tr}Created by{/tr} {displayname hash=$blog_data}{tr} on {/tr}{$created|bit_short_datetime}<br />
        {tr}Last modified{/tr} {$last_modified|bit_short_datetime}
    </div>
{/if}
</div>

<div class="footer">
    {$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity{/tr} {$activity|string_format:"%.2f"}
</div>


{section name=ix loop=$blogPosts}
	{include file="bitpackage:blogs/blog_list_post.tpl"}
{/section}

{pagination blog_id=$blog_id}

{if $use_find eq 'y'}
	{minifind blog_id=$blog_id sort_mode=$sort_mode}
{/if}

</div> {* end .blog *}
