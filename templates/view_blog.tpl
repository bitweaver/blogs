{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/view_blog.tpl,v 1.19 2007/03/20 19:29:29 spiderr Exp $ *}
{strip}
<div class="display blogs">
	<div class="floaticon">
		{if $gBitUser->hasPermission( 'p_blogs_post' )}
			{if $gBlog->hasEditPermission() or $gBlog->getField('is_public') eq 'y'}
				<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$gBlog->mBlogId}">{biticon ipackage="icons" iname="document-save" iexplain="post"}</a>
			{/if}
		{/if}

		{if $gBitSystem->isPackageActive( 'rss' )}
			<a title="{tr}RSS feed{/tr}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$gBlog->mBlogId}">{biticon ipackage="icons" iname="network-wireless" iexplain="RSS feed"}</a>
		{/if}

		{if $gBlog->hasEditPermission()}
			<a title="{tr}Edit blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$gBlog->mBlogId}">{biticon ipackage="icons" iname="document-properties" iexplain="edit"}</a>
		{/if}

		{if $gBitUser->isRegistered() and $gBitSystem->isFeatureActive( 'users_watches' )}
			{if $user_watching_blog eq 'n'}
				<a title="{tr}monitor this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$gBlog->mBlogId}&amp;watch_event=blog_post&amp;watch_object={$gBlog->mBlogId}&amp;watch_action=add">{biticon ipackage="icons" iname="weather-clear" iexplain="monitor this blog"}</a>
			{else}
				<a title="{tr}stop monitoring this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$gBlog->mBlogId}&amp;watch_event=blog_post&amp;watch_object={$gBlog->mBlogId}&amp;watch_action=remove">{biticon ipackage="icons" iname="weather-clear-night" iexplain="stop monitoring this blog"}</a>
			{/if}
		{/if}
		{if ($gBlog->hasEditPermission())}
			<a title="{tr}remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?remove=1&amp;blog_id={$gBlog->getField('blog_id')}">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
		{/if}
	</div>

	<div class="header">
		<h1>{$gBlog->getTitle()}</h1>
		{if $gBlog->getField('parsed')}<p>{$gBlog->getField('parsed')}</p>{/if}
		<div class="date">
			{tr}Created by{/tr}: {displayname hash=$gBlog->mInfo}, {$gBlog->getField('created')|bit_short_datetime}<br />
			{tr}Last modified{/tr}: {$gBlog->getField('last_modified')|bit_short_datetime}
		</div>
	</div>

	<div class="footer">
		{$gBlog->getField('postscant',0)} {tr}posts{/tr} | {$gBlog->getField('hits',0)} {tr}visits{/tr} {* TODO: Add back once activity is supported | {tr}Activity{/tr} {$gBlog->getField('activity',0)|string_format:"%.2f"} *}
	</div>

	{if $gBlog->getField('use_find') eq 'y'}
		{minifind blog_id=$gBlog->mBlogId sort_mode=$smarty.request.sort_mode}
	{/if}
		
	{foreach from=$blogPosts item=aPost}
		{include file="bitpackage:blogs/blog_list_post.tpl"}
	{foreachelse}
		<div class="norecords">{tr}No records found{/tr}</div>
	{/foreach}

	{pagination blog_id=$gBlog->mBlogId}
</div><!-- end .blogs -->
{/strip}
