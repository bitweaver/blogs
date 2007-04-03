{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.5 2007/04/03 16:31:18 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive('blogs')}
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
	{/bitmodule}
{/if}
{/strip}
