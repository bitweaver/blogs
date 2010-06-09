{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' )}
	{if $nonums eq 'y'}
		{eval var="{tr}Most `$module_rows` visited blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Most visited blogs{/tr}" assign="tpl_module_title"}
	{/if}
	
	{bitmodule title="$moduleTitle" name="top_visited_blogs"}
		<ol class="blogs">
			{foreach from=$modTopVisitedBlogs item=blog}
				<li><a href="{$blog.blog_url}">{$blog.title|escape|default:"Blog `$modTopVisitedBlogs[ix].blog_id`"}</a></li>
			{foreachelse}
				<li></li>
			{/foreach}
		</ol>
	{/bitmodule}
{/if}
{/strip}
