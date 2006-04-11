<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="{$uri}"
    dc:identifer="{$uri}"
    dc:title="{if $post_info.use_title eq 'y'}{$post_info.title|escape} {tr}posted by{/tr} {$post_info.user} on {$post_info.created|bit_short_datetime}{else}{$post_info.created|bit_short_datetime} {tr}posted by{/tr} {$post_info.user}{/if}"
    trackback:ping="{$uri2}" />
</rdf:RDF>
-->

{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$post_info}

<div class="display blogs">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$post_info}

		{if ($ownsblog eq 'y') or $gBitUser->hasPermission( 'p_blogs_admin' )}
			<a href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$post_info.blog_id}&amp;post_id={$post_info.post_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
			<a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$post_info.blog_id}&amp;remove={$post_info.post_id}">{biticon ipackage=liberty iname="delete" iexplain="delete"}</a>
		{/if}

		{if $gBitSystem->isPackageActive( 'notepad' ) and $gBitUser->hasPermission( 'bit_p_notepad' )}
			<a title="{tr}Save to notepad{/tr}" href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;savenotepad=1">{biticon ipackage=liberty iname="save" iexplain="save"}</a>
		{/if}
		{if $gBitUser->hasPermission( 'p_liberty_print' )}
			<a href="{$smarty.const.BLOGS_PKG_URL}print_blog_post.php?post_id={$post_id}">{biticon ipackage=liberty iname="print" iexplain="print"}</a>
		{/if}
		<a href="{$smarty.const.BLOGS_PKG_URL}send_post.php?post_id={$post_id}">{biticon ipackage=liberty iname="mail_send" iexplain="email this post"}</a>
	</div>

	<div class="header">
		<h1>
			{if $post_info.use_title eq 'y'}
				{$post_info.title|escape}
			{else}
				{$post_info.created|bit_long_date}
			{/if}
		</h1>
		<div class="date">
			{$post_info.created|bit_long_date}
		</div>
	</div>

	<div class="body"
	    {if $gBitUser->getPreference( 'user_dbl' ) eq 'y' and (($ownsblog eq 'y') or $gBitUser->hasPermission( 'p_blogs_admin' ))}
			ondblclick="location.href='{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$post_info.blog_id}&amp;post_id={$post_info.post_id}';"
		{/if}
	>
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$post_info}

			{$parsed_data}
			<p>
				{displayname hash=$post_info}<br />
				{tr}in{/tr} <a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$post_info.blog_id}">{$post_info.blogtitle}</a><br />
				{tr}Posted at{/tr} {$post_info.created|bit_long_time}
			</p>
		</div> <!-- end .content -->
	</div> <!-- end .body -->

	<div class="footer">
		<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?post_id={$post_id}">{tr}Permalink{/tr}</a>
		{tr}referenced by{/tr} {$post_info.trackbacks_from_count} {tr}posts{/tr} | {tr}references{/tr} {$post_info.trackbacks_to_count} {tr}posts{/tr}
		{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blogposts_comments' )}
			| {$post_info.num_comments} {tr}comments{/tr}
		{/if}
	</div> {* end .footer *}

	{if $pages > 1}
		<div class="pagination">
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$first_page}">{biticon ipackage=liberty iname="nav_first" iexplain="first page"}</a>
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$prev_page}">{biticon ipackage=liberty iname="nav_prev" iexplain="previous page"}</a>
			{tr}page{/tr}:{$page}/{$pages}
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$next_page}">{biticon ipackage=liberty iname="nav_next" iexplain="next page"}</a>
			<a href="{$smarty.const.BLOGS_PKG_URL}view_post.php?blog_id={$smarty.request.blog_id}&amp;post_id={$smarty.request.post_id}&amp;page={$last_page}">{biticon ipackage=liberty iname="nav_last" iexplain="last page"}</a>
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

{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blogposts_comments' )}
	{if $gBitUser->hasPermission( 'p_liberty_read_comments' )}
		{include file="bitpackage:liberty/comments.tpl"}
	{/if}
{/if}

{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$post_info}

