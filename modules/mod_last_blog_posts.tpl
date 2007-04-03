{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.tpl,v 1.6 2007/04/03 16:51:09 squareing Exp $ *}
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
					<a class="more" href="{$modLastBlogPosts[ix].post_url}">Read more</a>
				</li>
			{sectionelse}
				<li></li>
			{/section}
		</ul>
	{/bitmodule}
{/if}
{/strip}
