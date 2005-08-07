{strip}
<ul>
	{if $gBitUser->hasPermission( 'bit_p_read_blog' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}index.php">{tr}Recent Posts{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php">{tr}List Blogs{/tr}</a></li>
	{/if}{if $gBitUser->hasPermission( 'bit_p_create_blogs' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}edit.php">{tr}Create a Blog{/tr}</a></li>
	{/if}{if $gBitUser->hasPermission( 'bit_p_blog_post' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}post.php">{tr}Post to a Blog{/tr}</a></li>
	{/if}{if $gBitSystem->isFeatureActive( 'feature_blog_rankings' ) and $gBitUser->hasPermission( 'bit_p_read_blog' )}
		<li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}rankings.php">{tr}Blog Rankings{/tr}</a></li>
	{/if}
</ul>
{/strip}
