{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive('blogs')}
	{bitmodule title="$moduleTitle" name="last_blog_posts"}
		{if $blogPostsFormat == 'full'}
			<div class="blog">
				{foreach from=$modLastBlogPosts item=aPost}
					{include file="bitpackage:blogs/blog_list_post.tpl"}
				{/foreach}
			</div>
		{else}
			{include file="bitpackage:blogs/list_posts.tpl" blogPosts=$modLastBlogPosts}
		{/if}
	{/bitmodule}
{/if}
{/strip}
