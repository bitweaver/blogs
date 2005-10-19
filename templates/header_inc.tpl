{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.1.2.1 2005/10/19 22:29:59 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'bit_p_read_blog' )}
	<link rel="alternate" type="application/rss+xml" title="{$title}{$post_info.blogtitle}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$blog_id}" />
{/if}
{/strip}
