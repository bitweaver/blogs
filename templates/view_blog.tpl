{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/view_blog.tpl,v 1.24 2008/10/20 21:40:09 spiderr Exp $ *}
{strip}
<div class="display blogs">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$gContent->mInfo}

		{if $gContent->hasUserPermission( 'p_blogs_post' )}
			<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$gContent->mBlogId}">{biticon ipackage="icons" iname="document-save" iexplain="post"}</a>
		{/if}

		{if $gBitSystem->isPackageActive( 'rss' )}
			<a title="{tr}RSS feed{/tr}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$gContent->mBlogId}">{biticon ipackage="rss" iname="rss-16x16" iexplain="RSS feed"}</a>
		{/if}

		{if $gContent->hasUpdatePermission()}
			<a title="{tr}Edit blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$gContent->mBlogId}">{biticon ipackage="icons" iname="document-properties" iexplain="edit"}</a>
		{/if}

		{if $gBitUser->isRegistered() and $gBitSystem->isFeatureActive( 'users_watches' )}
			{if $user_watching_blog eq 'n'}
				<a title="{tr}monitor this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$gContent->mBlogId}&amp;watch_event=blog_post&amp;watch_object={$gContent->mBlogId}&amp;watch_action=add">{biticon ipackage="icons" iname="weather-clear" iexplain="monitor this blog"}</a>
			{else}
				<a title="{tr}stop monitoring this blog{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$gContent->mBlogId}&amp;watch_event=blog_post&amp;watch_object={$gContent->mBlogId}&amp;watch_action=remove">{biticon ipackage="icons" iname="weather-clear-night" iexplain="stop monitoring this blog"}</a>
			{/if}
		{/if}

		{if ($gContent->hasUpdatePermission())}
			<a title="{tr}remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?remove=1&amp;blog_id={$gContent->getField('blog_id')}">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
		{/if}
	</div>

	<div class="header">
		<h1>{$gContent->getTitle()}</h1>
		{if $gContent->getField('parsed')}<p>{$gContent->getField('parsed')}</p>{/if}
		<div class="date">
			{tr}Created by{/tr}: {displayname hash=$gContent->mInfo}, {$gContent->getField('created')|bit_short_datetime}<br />
			{tr}Last modified{/tr}: {$gContent->getField('last_modified')|bit_short_datetime}
		</div>
	</div>

	<div class="footer">
		{$gContent->getField('postscant',0)} {tr}posts{/tr} | {$gContent->getField('hits',0)} {tr}visits{/tr} {* TODO: Add back once activity is supported | {tr}Activity{/tr} {$gContent->getField('activity',0)|string_format:"%.2f"} *}
	</div>

	{if $gContent->getField('use_find') eq 'y'}
		{minifind blog_id=$gContent->mBlogId sort_mode=$smarty.request.sort_mode}
	{/if}
		
	{foreach from=$blogPosts item=aPost}
		{include file="bitpackage:blogs/blog_list_post.tpl"}
	{foreachelse}
		<div class="norecords">{tr}No records found{/tr}</div>
	{/foreach}

	{pagination blog_id=$gContent->mBlogId}
</div><!-- end .blogs -->
{/strip}
