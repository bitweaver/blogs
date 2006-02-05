{strip}

<div class="post"
	{if $gBitUser->getPreference( 'user_dbl' ) and (($aPost.ownsblog eq 'y') or ($gBitUser->mUserId and $aPost.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' ))}
		ondblclick="location.href='{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$aPost.blog_id}{$blog_id}&amp;post_id={$aPost.post_id}{$post_id}';"
	{/if}
>
	{if $gBitUser->hasPermission( 'bit_p_view_tabs_and_tools' )}
		<div class="floaticon">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$aPost}
			{if $gBitSystem->isPackageActive( 'rss' ) && $gBitSystem->isFeatureActive( 'rss_blogs' )}
				<a href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$aPost.blog_id}">{biticon ipackage=rss iname=rss iexplain="rss feed"}</a>
			{/if}
			{if ($aPost.ownsblog eq 'y') or ($gBitUser->mUserId and $aPost.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' )}
				<a title="{tr}Edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$aPost.blog_id}&amp;post_id={$aPost.post_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
				<a title="{tr}Remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$aPost.blog_id}&amp;remove={$aPost.post_id}">{biticon ipackage=liberty iname="delete" iexplain="delete"}</a>
			{/if}

			{**====== NOTEPAD PACKAGE CHECK IS BROKEN
			{if $gBitUser->mUserId and $gBitSystem->isPackageActive( 'notepad' ) and $gBitUser->hasPermission( 'bit_p_notepad' )}
				<a title="{tr}Save to notepad{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$aPost.blog_id}&amp;savenotepad={$aPost.post_id}">{biticon ipackage=liberty iname="save" iexplain="save"}</a>
			{/if}
			========= NOTEPAD PACKAGE CHECK IS BROKEN **}

			<a title="{tr}print{/tr}" href="{$smarty.const.BLOGS_PKG_URL}print_blog_post.php?post_id={$aPost.post_id}">{biticon ipackage=liberty iname="print" iexplain="print"}</a>
			<a title="{tr}email this post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}send_post.php?post_id={$aPost.post_id}">{biticon ipackage=liberty iname="mail_send" iexplain="email this post"}</a>
		</div>
	{/if}

	<div class="header">
		{if $aPost.title}
			<h1>{$aPost.title}</h1>
		{/if}

		<div class="date">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$aPost}
			{if $showBlogTitle}{displayname hash=$aPost} {tr}in{/tr} <a href="{$aPost.blog_url}">{$aPost.blogtitle}</a>{/if}<br />
			{$aPost.created|bit_long_date}
		</div>
	</div>

	<div class="body">
		<div class="content">
			{if $aPost.avatar}<img src="{$aPost.avatar}" class="avatar" />{/if}
			{$aPost.parsed_data}
			<p>
				{tr}Posted on {$aPost.created|bit_long_datetime}{/tr}
			</p>
		</div> <!-- end .content -->

		{if $aPost.pages > 1}
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$aPost.blog_id}&amp;post_id={$aPost.post_id}">{tr}read more{/tr} ({$aPost.pages} {tr}pages{/tr})</a>
		{/if}
	</div> <!-- end .body -->

	<div class="footer">
		<a href="{$aPost.post_url}">{tr}Permalink{/tr}</a>

		{if $aPost.trackbacks_from_count}({tr}referenced by{/tr}: {$aPost.trackbacks_from_count} {tr}posts{/tr} / {tr}references{/tr}: {$aPost.trackbacks_to_count} {tr}posts{/tr}){/if}

		{if $aPost.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'feature_blogposts_comments' )}
			&nbsp;{$aPost.num_comments} {if $aPost.num_comments == 1} {tr}comment{/tr} {else} {tr}comments{/tr}{/if} &nbsp;|&nbsp;
			 <a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?find={$find}&amp;blog_id={$aPost.blog_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;post_id={$aPost.post_id}&post_comment_request=1">{if $aPost.num_comments > 0}{tr}view comments{/tr}{else}{tr}add comment{/tr}{/if}</a>
		{/if}
	</div> <!-- end .footer -->
</div> <!-- end .blog -->

{/strip}
