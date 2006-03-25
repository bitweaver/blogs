{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.tpl,v 1.6 2006/03/25 20:47:40 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' )}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` Modified blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last Modified blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="last_modified_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modLastModifiedBlogs}
				<li><a href="{$modLastModifiedBlogs[ix].blog_url}">{$modLastModifiedBlogs[ix].title|escape|default:"Blog `$modLastModifiedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
