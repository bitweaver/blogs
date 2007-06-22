{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_blogs_view' )}
		{if $gBitSystem->isFeatureActive( 'blog_home' )}
			<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}index.php">{tr}Home Blog{/tr}</a></li>
		{/if}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}recent_posts.php">{tr}Recent Posts{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?sort_mode=last_modified_desc">{tr}List Blogs{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_create' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}edit.php">{tr}Create a Blog{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_post' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}post.php">{tr}Post{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'blog_rankings' ) and $gBitUser->hasPermission( 'p_blogs_view' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}rankings.php">{tr}Blog Post Rankings{/tr}</a></li>
	{/if}
</ul>
{/strip}
