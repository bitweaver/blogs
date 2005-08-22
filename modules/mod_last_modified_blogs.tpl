{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.tpl,v 1.1.1.1.2.3 2005/08/22 18:59:10 spiderr Exp $ *}
{strip}
{if $gBitSystem->isFeatureActive( 'feature_blogs' )}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` Modified blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last Modified blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="last_modified_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modLastModifiedBlogs}
				<li><a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$modLastModifiedBlogs[ix].blog_id}">{$modLastModifiedBlogs[ix].title|default:"Blog `$modLastModifiedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
