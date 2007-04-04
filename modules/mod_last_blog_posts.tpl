{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.7 2007/04/04 13:54:47 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive('blogs')}
	{bitmodule title="$moduleTitle" name="last_blog_posts"}
		<ul class="blogs">
			{section name=ix loop=$modLastBlogPosts}
				<li class="{cycle values="odd,even"}">
					<div class="title"><a href="{$modLastBlogPosts[ix].display_url}">{$modLastBlogPosts[ix].title}</a></div>
					<div class="date">{$modLastBlogPosts[ix].created|bit_long_date}
					<br />
					by {displayname hash=$modLastBlogPosts[ix]}</div>
					{$modLastBlogPosts[ix].parsed|truncate:$maxPreviewLength}
					<a class="more" href="{$modLastBlogPosts[ix].post_url}">Read more</a>
				</li>
			{sectionelse}
				<li></li>
			{/section}
		</ul>
	{/bitmodule}
{/if}
{/strip}
