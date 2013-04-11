{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' ) && $modLastCreatedBlogs}
	{if $nonums eq 'y'}
		{eval var="{tr}Last `$module_rows` Created blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Last Created blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="last_created_blogs"}
		<ol class="blogs">
			{foreach from=$modLastCreatedBlogs item=blogHash}
				<li><a href="{$blogHash.blog_url}">{$blogHash.title|escape|default:"Blog `$blogHash.blog_id`"}</a></li>
			{/foreach}
		</ol>
	{/bitmodule}
{/if}
{/strip}
