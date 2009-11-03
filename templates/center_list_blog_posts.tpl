{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/center_list_blog_posts.tpl,v 1.11 2009/11/03 15:41:42 wjames5 Exp $ *}
{if !( $smarty.request.home && $gBitSystem->isFeatureActive('blog_hide_empty_usr_list') ) }
<div class="floaticon">{bithelp}</div>

<div class="display blogs">
	<div class="header">
		<h1>{tr}Recent Blog Posts{/tr}</h1>
	</div>

	<div class="body">
		{if ($gBitUser->hasPermission( 'p_blog_posts_read_future' ) || $gBitUser->isAdmin() ) && $futures}
			<h3>{tr}Upcoming Blog Posts{/tr}</h3>
			<ul>
				{foreach from=$futures item=future}
					<li>{$future.display_link} <small>[ {tr}By:{/tr} {displayname hash=$future} | {tr}To be published{/tr}: {$future.publish_date|bit_long_datetime} ]</small></li>
				{/foreach}
			</ul>
			{if $blogPostsFormat == 'list'}
				<h3>{tr}Published Blog Posts{/tr}</h3>
			{/if}
		{/if}

		{if $blogPostsFormat == 'list'}
			{include file="bitpackage:blogs/list_posts.tpl"}
		{else}
			{foreach from=$blogPosts item=aPost}
				{include file="bitpackage:blogs/blog_list_post.tpl"}
			{foreachelse}
				<div class="norecords">{tr}No records found{/tr}</div>
			{/foreach}
		{/if}
	</div><!-- end .body -->

    {pagination url="`$paginationPath`" user_id="`$gQueryUserId`" blog_id="`$blogId`"}	

	{*minifind sort_mode=$sort_mode*}
</div>
{/if}
