{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.1 2005/06/19 03:57:42 bitweaver Exp $ *}
{strip}
{if $blogsPackageActive}
	{bitmodule title="$moduleTitle" name="last_blog_posts"}
		<ul class="blogs">
			{section name=ix loop=$modLastBlogPosts}
				<li class="{cycle values="odd,even"}">
					<div class="date">{$modLastBlogPosts[ix].created|bit_long_date}
					<br />
					by {displayname hash=$modLastBlogPosts[ix]}</div>
					{$modLastBlogPosts[ix].parsed_data|truncate:$maxPreviewLength}
					<br />
					<a href="{$modLastBlogPosts[ix].post_url}">Read more</a>
				</li>
			{sectionelse}
				<li></li>
			{/section}
		</ul>
		{if $user_blog_id}
			<div style="text-align:center;"><a href="{$gBitLoc.BIT_ROOT_URL}blogs/view.php?blog_id={$user_blog_id}">Visit my blog</a></div>
		{/if}
	{/bitmodule}
{/if}
{/strip}
