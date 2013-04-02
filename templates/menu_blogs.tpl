{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_blogs_view' )}
		{if $gBitSystem->isFeatureActive( 'blog_home' )}
			<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}index.php">{booticon iname="icon-home" iexplain="Home Blog" ilocation=menu}</a></li>
		{/if}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}recent_posts.php">{biticon iname="view-refresh" iexplain="Recent Posts" ilocation=menu}</a></li>
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?sort_mode=last_modified_desc">{booticon iname="icon-list" iexplain="List Blogs" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_create' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}edit.php">{biticon iname="folder-new" iexplain="Create a Blog" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_post' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}post.php">{booticon iname="icon-file" iexplain="Write Blog Post" ilocation=menu}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'blog_rankings' ) and $gBitUser->hasPermission( 'p_blogs_view' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}rankings.php">{booticon iname="icon-sort" iexplain="Blog Post Rankings" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
