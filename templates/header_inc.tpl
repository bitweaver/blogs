{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.7 2006/12/10 15:15:09 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'blogs_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	<link rel="alternate" type="application/rss+xml" title="{$gBitSystem->getConfig('blogs_rss_title',"{tr}Blogs{/tr} RSS")}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
{/if}
{/strip}
