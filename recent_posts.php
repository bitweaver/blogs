<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/recent_posts.php,v 1.12 2010/03/07 01:55:59 wjames5 Exp $
 * 
 * @package blogs
 * @subpackage functions
 */
 
/**
 * Initial Setup
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'blogs' );

require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

// Now check permissions to access this page
$gContent->verifyViewPermission();

if ( $gBitSystem->isFeatureActive( 'blog_ajax_more' ) && $gBitThemes->isJavascriptEnabled() ){
	$gBitSmarty->assign('ajax_more', TRUE);
	$gBitThemes->loadAjax( 'mochikit', array( 'Iter.js', 'DOM.js', 'Style.js', 'Color.js', 'Position.js', 'Visual.js' ) );
}

// Display the template
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' , array( 'display_mode' => 'display' ));
?>
