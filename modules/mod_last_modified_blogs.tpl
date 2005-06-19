{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.tpl,v 1.1 2005/06/19 03:57:42 bitweaver Exp $ *}
{strip}
{if $gBitSystemPrefs.feature_blogs eq 'y'}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` Modified blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last Modified blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="last_modified_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modLastModifiedBlogs}
				<li><a href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$modLastModifiedBlogs[ix].blog_id}">{$modLastModifiedBlogs[ix].title}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
