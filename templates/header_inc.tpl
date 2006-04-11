{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.4 2006/04/11 13:03:38 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} RSS" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=rss20" />
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} ATOM" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=atom" />
{/if}
{/strip}
