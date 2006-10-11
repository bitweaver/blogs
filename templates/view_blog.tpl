{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/view_blog.tpl,v 1.12 2006/10/11 06:05:13 spiderr Exp $ *}
{strip}
<div class="display blogs">
	<div class="floaticon">
		{if $gBitUser->hasPermission( 'p_blogs_post' )}
			{if ($gBitUser->mUserId and $creator eq $gBitUser->mUserId) or $gBitUser->object_has_permission($gBitUser->mUserId, $blog_id, 'bitblog', 'p_blogs_post') or $gBitUser->hasPermission( 'p_blogs_admin' ) or $is_public eq 'y'}
				<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$blog_id}">{biticon ipackage="icons" iname="mail-forward" iexplain="post"}</a>
			{/if}
		{/if}

		{if $gBitSystem->isPackageActive( 'rss' ) && $rss_blog eq 'y'}
			<a title="{tr}RSS feed{/tr}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$blog_id}">{biticon ipackage="icons" iname="network-wireless" iexplain="RSS feed"}</a>
		{/if}

		{if ($gBitUser->mUserId and $creator eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
			<a title="{tr}Edit blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$blog_id}">{biticon ipackage="icons" iname="document-properties" iexplain="edit"}</a>
		{/if}

		{if $gBitUser->isRegistered() and $gBitSystem->isFeatureActive( 'users_watches' )}
			{if $user_watching_blog eq 'n'}
				<a title="{tr}monitor this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=add">{biticon ipackage="icons" iname="weather-clear" iexplain="monitor this blog"}</a>
			{else}
				<a title="{tr}stop monitoring this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=remove">{biticon ipackage="icons" iname="weather-clear-night" iexplain="stop monitoring this blog"}</a>
			{/if}
		{/if}
	</div>

	<div class="header">
		<h1>{$title}</h1>
		{if $description}<h2>{$description}</h2>{/if}
		{if strlen($heading) > 0}
			<div class="introduction">{eval var=$heading}</div>
		{else}
			<div class="date">
				{tr}Created by{/tr}: {displayname hash=$blog_data}, {$created|bit_short_datetime}<br />
				{tr}Last modified{/tr}: {$last_modified|bit_short_datetime}
			</div>
		{/if}
	</div>

	<div class="footer">
		{$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity{/tr} {$activity|string_format:"%.2f"}
	</div>
		
	{foreach from=$blogPosts item=aPost}
		{include file="bitpackage:blogs/blog_list_post.tpl"}
	{foreachelse}
		<div class="norecords">{tr}No records found{/tr}</div>
	{/foreach}

	{pagination blog_id=$blog_id}

	{if $use_find eq 'y'}
		{minifind blog_id=$blog_id sort_mode=$sort_mode}
	{/if}
</div><!-- end .blogs -->
{/strip}
