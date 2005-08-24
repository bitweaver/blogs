{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_created_blogs.tpl,v 1.4 2005/08/24 20:49:32 squareing Exp $ *}
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
				<li><a href="{$modLastCreatedBlogs[ix].blog_url}">{$modLastCreatedBlogs[ix].title|default:"Blog `$modLastCreatedBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
