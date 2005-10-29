{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.1.2.2 2005/10/29 10:00:25 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'bit_p_read_blog' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} RSS" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=rss20" />
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} ATOM" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=atom" />
{/if}
{/strip}
