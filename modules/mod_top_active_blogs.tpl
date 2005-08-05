{* $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.tpl,v 1.1.1.1.2.2 2005/08/05 22:59:51 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'blogs' ) && $gBitUser->hasPermission( 'bit_p_read_blog' )}
	{if $nonums eq 'y'}
		{eval var="`$module_rows` {tr}Most Active blogs{/tr}" assign="tpl_module_title"}
	{else}
		{eval var="{tr}Most Active blogs{/tr}" assign="tpl_module_title"}
	{/if}
	{bitmodule title="$moduleTitle" name="top_active_blogs"}
		<ol class="blogs">
			{section name=ix loop=$modTopActiveBlogs}
				<li><a href="{$smarty.const.BLOGS_PKG_URL}view.php?blog_id={$modTopActiveBlogs[ix].blog_id}">{$modTopActiveBlogs[ix].title}</a></li>
			{sectionelse}
				<li></li>
			{/section}
		</ol>
	{/bitmodule}
{/if}
{/strip}
