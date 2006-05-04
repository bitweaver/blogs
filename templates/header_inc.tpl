{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.5 2006/05/04 19:04:58 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'blogs_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} RSS" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=rss20" />
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} ATOM" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version=atom" />
{/if}
{/strip}
