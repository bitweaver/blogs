{strip}
{* This template is used by the blogs plugin to liberty. *}
<div class="post"
	{if $gBitUser->getPreference( 'users_double_click' ) and (($aPost.ownsblog eq 'y') or ($gBitUser->mUserId and $aPost.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' ))}
		ondblclick="location.href='{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$aPost.blog_id}{$blog_id}&amp;post_id={$aPost.post_id}{$post_id}';"
	{/if}
>
	{if $gBitUser->hasPermission( 'p_users_view_icons_and_tools' )}
		<div class="floaticon">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$aPost}
			{if $gBitUser->hasPermission( 'p_blogs_admin' )}
				<a title="{tr}Crosspost{/tr}" href="{$smarty.const.BLOGS_PKG_URL}crosspost.php?post_id={$aPost.post_id}">{biticon ipackage="icons" iname="mail-attachment" iexplain="crosspost"}</a>
			{/if}

			<a title="{tr}print{/tr}" style="display:none;" href="{$smarty.const.BLOGS_PKG_URL}print_blog_post.php?post_id={$aPost.post_id}">{biticon ipackage="icons" iname="document-print" iexplain="print"}</a>
			{if $gBitUser->hasPermission('p_blogs_send_post')}
				<a title="{tr}email this post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}send_post.php?post_id={$aPost.post_id}">{biticon ipackage="icons" iname="mail-forward" iexplain="email this post"}</a>
			{/if}

			{if ($aPost.ownsblog eq 'y') or ($gBitUser->mUserId and $aPost.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
				<a title="{tr}Edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$aPost.blog_id}&amp;post_id={$aPost.post_id}">{booticon iname="icon-edit" ipackage="icons" iexplain="edit"}</a>
				<a title="{tr}Remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?action=remove&amp;post_id={$aPost.post_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="delete"}</a>
			{/if}
		</div>
	{/if}

	<div class="header">
		<h1>
		{if $aPost.title}
			{$aPost.title|escape:html}
		{else}
			{$aPost.publish_date|default:$aPost.created|bit_long_date}
		{/if}
		</h1>

		<div class="date">
			{if $gBitSystem->getConfig('blog_list_user_as') eq 'link'}
				{tr}By{/tr} {displayname hash=$aPost}
			{elseif $gBitSystem->getConfig('blog_list_user_as') eq 'avatar' && $aPost.avatar}
				<img src="{$aPost.avatar}" class="avatar" />
			{else}
				{tr}By{/tr} {displayname hash=$aPost nolink=true}
			{/if}<br/>

			{$aPost.publish_date|default:$aPost.created|bit_long_date}<br />
			{if count($aPost.blogs) > 0}
				{tr}Posted to{/tr}&nbsp;
				{foreach from=$aPost.blogs item=memberBlog key=blogContentId name=memberBlogLoop}
					<a href="{$memberBlog.blog_url}">{$memberBlog.title}</a>{if $smarty.foreach.memberBlogLoop.total > 1 && !$smarty.foreach.memberBlogLoop.last }, {/if}
				{/foreach}
				<br />
			{/if}
		</div>
	</div>

	<div class="body">
		<div class="content">
			{if $aPost.crosspost_note}
				<div class="bitbox">{$aPost.crosspost_note}</div>
			{/if}
			
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$aPost}
			
			{* deal with the blog post image if there is one *}
			{if $gBitSystem->isFeatureActive( 'blog_show_image' ) && $aPost.thumbnail_url}
				<div class="image">
					{jspopup notra=1 href=$aPost.thumbnail_url.original alt=$aPost.title|escape title=$aPost.title|escape" img=$aPost.thumbnail_url.medium}
				</div>
			{/if}
			
			{if $showDescriptionsOnly}
				{$aPost.summary|default:$aPost.parsed_description}
				{if $ajax_more}<div id="post_more_{$aPost.post_id}"></div>{/if}
			{else}
				{$aPost.parsed_data}
			{/if}
			{* this is at the top of the post <p>{tr}Posted on {$aPost.publish_date|default:$aPost.created|bit_long_datetime}{/tr}</p> *}
		</div><!-- end .content -->

		{if $aPost.pages > 1}
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$aPost.blog_id}&amp;post_id={$aPost.post_id}">{tr}read more{/tr} ({$aPost.pages} {tr}pages{/tr})</a>
		{/if}
	</div> <!-- end .body -->

	<div class="footer">
		<a href="{$aPost.post_url}" rel="bookmark">{tr}Permalink{/tr}</a>
		{assign var=spacer value=TRUE}

		{if $showDescriptionsOnly and $aPost.has_more}
			{if $spacer}&nbsp; &bull; &nbsp;{/if}
			{assign var=spacer value=TRUE}
			{if $ajax_more}
				<a href="javascript:void(0);" onclick="BitAjax.updater( 'post_more_{$aPost.post_id}', '{$smarty.const.BLOGS_PKG_URL}view_post.php', 'blog_id={$aPost.blog_id}&post_id={$aPost.post_id}&format={if $aPost.summary}data{else}more{/if}&output=ajax' )">{tr}Read More{/tr}</a>
			{else}
				<a class="more" href="{$aPost.display_url}">{tr}Read More&hellip;{/tr}</a>
			{/if}
		{/if}

		{if $aPost.trackbacks_from_count}({tr}referenced by{/tr}: {$aPost.trackbacks_from_count} {tr}posts{/tr} / {tr}references{/tr}: {$aPost.trackbacks_to_count} {tr}posts{/tr}){/if}

		{if $gBitSystem->isFeatureActive( 'blog_posts_comments' )}
			{if $spacer}&nbsp; &bull; &nbsp;{/if}
			&nbsp;<span {if $aPost.num_comments > 0}class="commented"{/if}>{$aPost.num_comments}&nbsp;{if $aPost.num_comments == 1}{tr}comment{/tr}{else}{tr}comments{/tr}{/if}</span> &nbsp;|&nbsp;
			 <a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?&amp;post_id={$aPost.post_id}&amp;post_comment_request={if $aPost.num_comments > 0}1{else}y{/if}">{if $aPost.num_comments > 0}{tr}view comments{/tr}{else}{tr}add comment{/tr}{/if}</a>
		{/if}
	</div> <!-- end .footer -->
</div> <!-- end .blog -->

{/strip}
