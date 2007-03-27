<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="{$uri}"
    dc:identifer="{$uri}"
    dc:title="{if $post_info.use_title eq 'y'}{$post_info.title|escape} {tr}posted by{/tr} {$post_info.user} on {$post_info.publish_date|default:$post_info.created|bit_short_datetime}{else}{$post_info.publish_date|default:$post_info.created|bit_short_datetime} {tr}posted by{/tr} {$post_info.user}{/if}"
    trackback:ping="{$uri2}" />
</rdf:RDF>
-->

{strip}
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$post_info}

<div class="display blogs">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$post_info}
		{if $gBitUser->hasPermission( 'p_users_view_icons_and_tools' )}
			{if $gBitSystem->isPackageActive( 'rss' ) && $gBitSystem->isFeatureActive( 'rss_blogs' )}
				<a href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?user_id={$post_info.user_id}">{biticon ipackage="icons" iname="network-wireless" iexplain="rss feed"}</a>
			{/if}

			{if $gBitUser->hasPermission( 'p_blogs_admin' )}
				<a title="{tr}Crosspost{/tr}" href="{$smarty.const.BLOGS_PKG_URL}crosspost.php?post_id={$post_info.post_id}">{biticon ipackage="icons" iname="mail-attachment" iexplain="crosspost"}</a>
			{/if}

			<a title="{tr}print{/tr}" style="display:none;" href="{$smarty.const.BLOGS_PKG_URL}print_blog_post.php?post_id={$post_info.post_id}">{biticon ipackage="icons" iname="document-print" iexplain="print"}</a>
			{if $gBitUser->hasPermission('p_blogs_send_post')}
				<a title="{tr}email this post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}send_post.php?post_id={$post_info.post_id}">{biticon ipackage="icons" iname="mail-forward" iexplain="email this post"}</a>
			{/if}

			{if $gContent->hasEditPermission()}
				<a title="{tr}Edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$post_info.blog_id}&amp;post_id={$post_info.post_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="edit"}</a>
				<a title="{tr}Remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?action=remove&amp;remove_post_id={$post_info.post_id}&amp;status_id=300">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
			{/if}
		{/if}
	</div>

	<div class="header">
		{if $post_info.blogtitle}
			<h1>{$post_info.blogtitle|escape}</h1>
		{/if}

		<div class="navigation">
			{if $gContent_previous}
				<span class="left">
					Previous: <a href="{$gContent_previous->getDisplayUrl()}">{$gContent_previous->mInfo.title|escape}</a>
				</span>
			{/if}

			{if $gContent_next}
				<span class="right">
					Next: <a href="{$gContent_next->getDisplayUrl()}">{$gContent_next->mInfo.title|escape}</a>
				</span>
			{/if}
		</div>

		<h1>
			{if $post_info.use_title eq 'y'}
				{$post_info.title|escape}
			{else}
				{$post_info.publish_date|default:$post_info.created|bit_long_date}
			{/if}
		</h1>

		<div class="date">
			{tr}by {displayname hash=$post_info}{/tr}<br />
			{$post_info.publish_date|default:$post_info.created|bit_long_date}<br />
			{if count($post_info.blogs) > 0}
				{tr}Posted to{/tr}&nbsp;
				{foreach from=$post_info.blogs item=memberBlog key=blogContentId name=memberBlogLoop}
					<a href="{$memberBlog.blog_url}">{$memberBlog.title}</a>{if $smarty.foreach.memberBlogLoop.total > 1 && !$smarty.foreach.memberBlogLoop.last }, {/if}
				{/foreach}
			<br />
			{/if}
		</div>
	</div>

	<div class="body"
	    {if $gBitUser->getPreference( 'users_double_click' ) eq 'y' and (($ownsblog eq 'y') or $gBitUser->hasPermission( 'p_blogs_admin' ))}
			ondblclick="location.href='{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$post_info.blog_id}&amp;post_id={$post_info.post_id}';"
		{/if}
	>
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$post_info}

			{$parsed_data}
		</div> <!-- end .content -->
	</div> <!-- end .body -->

	<div class="footer">
		<a href="{$post_info.url}">{tr}Permalink{/tr}</a> |&nbsp;
		{tr}referenced by{/tr} {$post_info.trackbacks_from_count} {tr}posts{/tr} | {tr}references{/tr} {$post_info.trackbacks_to_count} {tr}posts{/tr}
		{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blog_posts_comments' )}
			| {$post_info.num_comments} {tr}comments{/tr}
		{/if}
	</div> {* end .footer *}

	{if $pages > 1}
		<div class="pagination">
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$first_page}">{biticon ipackage="icons" iname="go-first" iexplain="first page"}</a>
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$prev_page}">{biticon ipackage="icons" iname="go-previous" iexplain="previous page"}</a>
			{tr}page{/tr}:{$page}/{$pages}
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$next_page}">{biticon ipackage="icons" iname="go-next" iexplain="next page"}</a>
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$last_page}">{biticon ipackage="icons" iname="go-last" iexplain="last page"}</a>
		</div>
	{/if}

	{if $post_info.trackbacks_from_count > 0}
		<table>
			<caption>{tr}Trackback Pings{/tr}</caption>
				<tr>
					<th>{tr}Title{/tr}</th>
					<th>{tr}URI{/tr}</th>
					<th>{tr}Blog name{/tr}</th>
				</tr>
			{cycle values="even,odd" print=false}
			{foreach from=$post_info.trackbacks_from key=key item=item}
				<tr class="{cycle}">
					<td>{$item.title|escape}</td>
					<td><a href="{$key}" title="{$key}" class="external">{$key|truncate:"40"}</a></td>
					<td>{$item.blog_name}</td>
				</tr>
			{/foreach}
		</table>
	{/if}
</div> {* end .blog *}

{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blog_posts_comments' )}
	{if $gBitUser->hasPermission( 'p_liberty_read_comments' )}
		{include file="bitpackage:liberty/comments.tpl"}
	{/if}
{/if}

{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$post_info}
{/strip}
