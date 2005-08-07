{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/center_list_blog_posts.tpl,v 1.2 2005/08/07 17:35:55 squareing Exp $ *}
{if $blogPosts || $showEmpty}
<div class="floaticon">{bithelp}</div>

<div class="display blogs">
	<div class="header">
		<h1>{tr}Recent Blog Posts{/tr}</h1>
	</div>

	<div class="body">
		{section name=ix loop=$blogPosts}
			{include file="bitpackage:blogs/blog_list_post.tpl"}
		{sectionelse}
			<div class="body">
				<div class="norecords">{tr}No records found{/tr}</div>
			</div>
		{/section}
	</div>

	{pagination url="`$smarty.const.BLOGS_PKG_URL`index.php" user_id="`$gQueryUserId`"}

	{*minifind sort_mode=$sort_mode*}
</div>
{/if}