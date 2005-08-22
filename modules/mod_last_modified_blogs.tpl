{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.tpl,v 1.1.1.1.2.4 2005/08/22 19:26:05 spiderr Exp $ *}
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
				<li><a href="{$modLastModifiedBlogs[ix].blog_url}">{$modLastModifiedBlogs[ix].title|default:"Blog `$modLastModifiedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
