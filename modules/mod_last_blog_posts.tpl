{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.4 2007/03/27 21:54:30 wjames5 Exp $ *}
{strip}
{if $blogsPackageActive}
	{bitmodule title="$moduleTitle" name="last_blog_posts"}
		<ul class="blogs">
			{section name=ix loop=$modLastBlogPosts}
				<li class="{cycle values="odd,even"}">
					<div class="title">{$modLastBlogPosts[ix].title}</div>			
					<div class="date">{$modLastBlogPosts[ix].created|bit_long_date}
					<br />
					by {displayname hash=$modLastBlogPosts[ix]}</div>
					{$modLastBlogPosts[ix].parsed|truncate:$maxPreviewLength}
					<a href="{$modLastBlogPosts[ix].post_url}">Read more</a>
				</li>
			{sectionelse}
				<li></li>
			{/section}
		</ul>
		{* DEPRECATED Slated for removal - only legacy sites have user blogs -wjames5
		{if $user_blog_id}
			<div style="text-align:center;"><a href="{$smarty.const.BIT_ROOT_URL}blogs/view.php?blog_id={$user_blog_id}">Visit my blog</a></div>
		{/if}
		*}
	{/bitmodule}
{/if}
{/strip}
