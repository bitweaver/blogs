{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.6 2006/12/09 23:42:07 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'blogs_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Blogs{/tr} RSS" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
{/if}
{/strip}
