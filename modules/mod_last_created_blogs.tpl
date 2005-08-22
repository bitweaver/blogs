{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_created_blogs.tpl,v 1.1.1.1.2.3 2005/08/22 18:59:10 spiderr Exp $ *}
{strip}
{if $gBitSystem->isFeatureActive( 'feature_blogs' )}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` Created blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last Created blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="last_created_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modLastCreatedBlogs}
				<li><a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$modLastCreatedBlogs[ix].blog_id}">{$modLastCreatedBlogs[ix].title|default:"Blog `$modLastCreatedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
