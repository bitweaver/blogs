{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_blogs_view' )}
		{if $gBitSystem->isFeatureActive( 'blog_home' )}
			<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}index.php">{biticon ipackage="icons" iname="go-home" iexplain="Home Blog" iforce="icon"} {tr}Home Blog{/tr}</a></li>
		{/if}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}recent_posts.php">{biticon ipackage="icons" iname="document-new" iexplain="Recent Posts" iforce="icon"} {tr}Recent Posts{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?sort_mode=last_modified_desc">{biticon ipackage="icons" iname="format-justify-fill" iexplain="List Blogs" iforce="icon"} {tr}List Blogs{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_create' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}edit.php">{biticon ipackage="icons" iname="mail-message-new" iexplain="Create a Blog" iforce="icon"} {tr}Create a Blog{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_blogs_post' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}post.php">{biticon ipackage="icons" iname="document-save" iexplain="Post" iforce="icon"} {tr}Post{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'blog_rankings' ) and $gBitUser->hasPermission( 'p_blogs_view' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}rankings.php">{biticon ipackage="icons" iname="view-sort-ascending" iexplain="Blog Post Rankings" iforce="icon"} {tr}Blog Post Rankings{/tr}</a></li>
	{/if}
</ul>
{/strip}
