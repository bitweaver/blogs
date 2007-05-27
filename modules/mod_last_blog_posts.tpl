{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.10 2007/05/27 04:38:56 laetzer Exp $ *}
{strip}
{if $gBitSystem->isPackageActive('blogs')}
	{bitmodule title="$moduleTitle" name="last_blog_posts"}
		{if $modLastBlogPostsFormat == 'full'}
			<div class="blog">
				{foreach from=$modLastBlogPosts item=aPost}
					{include file="bitpackage:blogs/blog_list_post.tpl"}
				{/foreach}
			</div>
		{else}
			<ul class="blogs">
				{section name=ix loop=$modLastBlogPosts}
					<li class="{cycle values="odd,even"}">
						<div class="title">
							<a href="{$modLastBlogPosts[ix].display_url}">
								{if $modLastBlogPosts[ix].title}
									{$modLastBlogPosts[ix].title}
								{else}
									{$modLastBlogPosts[ix].publish_date|default:$modLastBlogPosts[ix].created|bit_long_date}
								{/if}
							</a>
						</div>
						<div class="date">
							{$modLastBlogPosts[ix].created|bit_long_date}
							<br />
							{tr}by{/tr} {displayname hash=$modLastBlogPosts[ix]}
						</div>
						{$modLastBlogPosts[ix].parsed|truncate:$maxPreviewLength}
						<a class="more" href="{$modLastBlogPosts[ix].post_url}">{tr}read more{/tr}</a>
					</li>
				{sectionelse}
					<li></li>
				{/section}
			</ul>
		{/if}
	{/bitmodule}
{/if}
{/strip}
