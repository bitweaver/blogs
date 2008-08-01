{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/list_posts.tpl,v 1.4 2008/08/01 02:10:39 laetzer Exp $ *}
{strip}
<ul class="blogs">
	{section name=ix loop=$blogPosts}
		<li class="{cycle values="odd,even"}">
			<div class="title">
				<a href="{$blogPosts[ix].display_url}">
					{if $blogPosts[ix].title}
						{$blogPosts[ix].title}
					{else}
						{$blogPosts[ix].publish_date|default:$blogPosts[ix].created|bit_long_date}
					{/if}
				</a>
			</div>
			<div class="date">
				{$blogPosts[ix].publish_date|default:$blogPosts[ix].created|bit_long_date}
				<br />
				{tr}by{/tr} {displayname hash=$blogPosts[ix]}
			</div>
			{if $blogPostsFormat == 'summary'}
				{$blogPosts[ix].summary|default:$blogPosts[ix].parsed_description|truncate:$descriptionLength}
				<a class="more" href="{$blogPosts[ix].post_url}">{tr}read more{/tr}</a>
			{/if}
		</li>
	{sectionelse}
		<li></li>
	{/section}
</ul>
{/strip}