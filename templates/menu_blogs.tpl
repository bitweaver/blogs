{strip}
<ul>
        {if $gBitUser->hasPermission( 'p_blogs_view' )}
                <li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}index.php">{tr}Recent Posts{/tr}</a></li>
                <li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?sort_mode=last_modified_desc">{tr}List Blogs{/tr}</a></li>
        {/if}{if $gBitUser->hasPermission( 'p_blogs_create' )}
                <li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}edit.php">{tr}Create a Blog{/tr}</a></li>
        {/if}{if $gBitUser->hasPermission( 'p_blogs_post' )}
                <li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}post.php">{tr}Post to a Blog{/tr}</a></li>
        {/if}{if $gBitSystem->isFeatureActive( 'blog_rankings' ) and $gBitUser->hasPermission( 'p_blogs_view' )}
                <li><a class="item" href="{$smarty.const.BLOGS_PKG_URL}rankings.php">{tr}Blog Rankings{/tr}</a></li>
        {/if}
</ul>
{/strip}