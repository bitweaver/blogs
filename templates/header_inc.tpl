{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/header_inc.tpl,v 1.8 2007/11/05 21:35:22 wjames5 Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'blogs_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	<link rel="alternate" type="application/rss+xml" title="{$gBitSystem->getConfig('blogs_rss_title',"{tr}Blogs{/tr} RSS")}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
{/if}
{* this is for ajaxing the readmore portion of blog posts. 
 * this is ugly, but because recent posts are handled by dynamic center outside of the blog package we have 
 * no way to assign the appropriate ajax library needs the propery way - so we force it here.
 *
 * this is configured for using center_list_blog_posts in the user pkg, as this is a common configuration,
 * but it can be used in other pkgs, like wiki for example. Expand this set of conditionals as
 * needed for your site configuation by creating a custom version of this tpl in your theme. Target the 
 * conditionals to the pkg you are including center_list_blog_posts in.
 *}
{if $gBitSystem->isFeatureActive( 'blog_ajax_more' ) && !$gBitThemes->isAjaxLib('mochikit') && ( $smarty.const.ACTIVE_PACKAGE == "users" && $gQueryUserId ) }
	<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/MochiKitBitAjax.js"></script>
	<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/MochiKit/Base.js"></script>
	<script type="text/javascript" src="{$smarty.const.UTIL_PKG_URL}javascript/libs/MochiKit/Async.js"></script>
{/if}
{/strip}
