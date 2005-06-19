{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.tpl,v 1.1 2005/06/19 03:57:42 bitweaver Exp $ *}
{strip}
{if $gBitSystemPrefs.package_blogs eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Most `$module_rows` visited blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Most visited blogs{/tr}" assign="tpl_module_title"}
	{/if}

	{bitmodule title="$moduleTitle" name="top_visited_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modTopVisitedBlogs}
				<li><a href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$modTopVisitedBlogs[ix].blog_id}">{$modTopVisitedBlogs[ix].title}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
