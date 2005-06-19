{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/list_posts.tpl,v 1.1 2005/06/19 03:57:42 bitweaver Exp $ *}
<div class="floaticon">{bithelp}</div>  

<div class="listing blogs">
	<div class="header"><h1>{tr}Recent Blog Posts{/tr}</h1></div>

	<div class="body">
		{section name=ix loop=$blogPosts}
			{include file="bitpackage:blogs/blog_list_post.tpl"}
			<a href="{$blogPosts[ix].post_url}">{tr}read post{/tr}</a>
		{sectionelse}
			<div class="norecords">{tr}No records found{/tr}</div>
		{/section}
	</div>

	{pagination}
</div>
