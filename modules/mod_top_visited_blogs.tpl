{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.tpl,v 1.5 2006/03/25 20:47:40 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' )}
	{if $nonums eq 'y'}
		{eval var="{tr}Most `$module_rows` visited blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Most visited blogs{/tr}" assign="tpl_module_title"}
	{/if}

	{bitmodule title="$moduleTitle" name="top_visited_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modTopVisitedBlogs}
				<li><a href="{$modTopVisitedBlogs[ix].blog_url}">{$modTopVisitedBlogs[ix].title|escape|default:"Blog `$modTopVisitedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
