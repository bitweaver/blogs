{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' ) && $gBitUser->hasPermission( 'p_blogs_view' )}
	{if $nonums eq 'y'}
		{eval var="`$module_rows` {tr}Most Active blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Most Active blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="top_active_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modTopActiveBlogs}
				<li><a href="{$modTopActiveBlogs[ix].blog_url}">{$modTopActiveBlogs[ix].title|escape|default:"Blog `$modTopActiveBlogs[ix].blog_id`"}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
