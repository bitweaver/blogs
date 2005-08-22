{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.tpl,v 1.1.1.1.2.3 2005/08/22 18:59:10 spiderr Exp $ *}
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
				<li><a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$modTopVisitedBlogs[ix].blog_id}">{$modTopVisitedBlogs[ix].title|default:"Blog `$modTopVisitedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
