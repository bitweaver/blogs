{strip}

<div class="post"
	{if $user_dbl eq 'y' and (($blogPosts[ix].ownsblog eq 'y') or ($gBitUser->mUserId and $blogPosts[ix].user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' ))}
		ondblclick="location.href='{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$blogPosts[ix].blog_id}{$blog_id}&amp;post_id={$blogPosts[ix].post_id}{$post_id}';"
	{/if}
>
	{if $gBitUser->hasPermission( 'bit_p_view_tabs_and_tools' )}
		<div class="floaticon">
			{if $gBitSystem->isPackageActive( 'rss' ) && $gBitSystem->isFeatureActive( 'rss_blogs' )}
				<a href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$blogPosts[ix].blog_id}">{biticon ipackage=rss iname=rss iexplain="rss feed"}</a>
			{/if}
			{if ($blogPosts[ix].ownsblog eq 'y') or ($gBitUser->mUserId and $blogPosts[ix].user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' )}
				<a title="{tr}Edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$blogPosts[ix].blog_id}&amp;post_id={$blogPosts[ix].post_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
				<a title="{tr}Remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$blogPosts[ix].blog_id}&amp;remove={$blogPosts[ix].post_id}">{biticon ipackage=liberty iname="delete" iexplain="delete"}</a>
			{/if}

			{**====== NOTEPAD PACKAGE CHECK IS BROKEN
			{if $gBitUser->mUserId and $gBitSystem->isPackageActive( 'notepad' ) and $gBitUser->hasPermission( 'bit_p_notepad' )}
				<a title="{tr}Save to notepad{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$blogPosts[ix].blog_id}&amp;savenotepad={$blogPosts[ix].post_id}">{biticon ipackage=liberty iname="save" iexplain="save"}</a>
			{/if}
			========= NOTEPAD PACKAGE CHECK IS BROKEN **}

			<a title="{tr}print{/tr}" href="{$smarty.const.BLOGS_PKG_URL}print_blog_post.php?post_id={$blogPosts[ix].post_id}">{biticon ipackage=liberty iname="print" iexplain="print"}</a>
			<a title="{tr}email this post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}send_post.php?post_id={$blogPosts[ix].post_id}">{biticon ipackage=liberty iname="mail_send" iexplain="email this post"}</a>
		</div>
	{/if}

	<div class="header">
		{if $blogPosts[ix].title}
			<h1>{$blogPosts[ix].title}</h1>
		{else}
			<h1>{$blogPosts[ix].created|bit_long_date}</h1>
		{/if}

		<div class="date">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}
			{if $showBlogTitle}{displayname hash=$blogPosts[ix]} {tr}in{/tr} <a href="{$blogPosts[ix].blog_url}">{$blogPosts[ix].blogtitle}</a>{/if}<br />
			{$blogPosts[ix].created|bit_long_date}
		</div>
	</div>

	<div class="body">
		<div class="content">
			{if $blogPosts[ix].avatar}<img src="{$blogPosts[ix].avatar}" class="avatar" />{/if}
			{$blogPosts[ix].parsed_data}
			<p>
				{tr}Posted on {$blogPosts[ix].created|bit_long_datetime}{/tr}
			</p>
		</div> <!-- end .content -->

		{if $blogPosts[ix].pages > 1}
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$blogPosts[ix].blog_id}&amp;post_id={$blogPosts[ix].post_id}">{tr}read more{/tr} ({$blogPosts[ix].pages} {tr}pages{/tr})</a>
		{/if}
	</div> <!-- end .body -->

	<div class="footer">
		<a href="{$blogPosts[ix].post_url}">{tr}Permalink{/tr}</a>

		{if $blogPosts[ix].trackbacks_from_count}({tr}referenced by{/tr}: {$blogPosts[ix].trackbacks_from_count} {tr}posts{/tr} / {tr}references{/tr}: {$blogPosts[ix].trackbacks_to_count} {tr}posts{/tr}){/if}

		{if $blogPosts[ix].allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'feature_blogposts_comments' )}
			&nbsp;{$blogPosts[ix].num_comments} {if $blogPosts[ix].num_comments == 1} {tr}comment{/tr} {else} {tr}comments{/tr}{/if} &nbsp;|&nbsp;
			 <a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?find={$find}&amp;blog_id={$blogPosts[ix].blog_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;post_id={$blogPosts[ix].post_id}&post_comment_request=1">{if $blogPosts[ix].num_comments > 0}{tr}view comments{/tr}{else}{tr}add comment{/tr}{/if}</a>
		{/if}
	</div> <!-- end .footer -->
</div> <!-- end .blog -->

{/strip}
