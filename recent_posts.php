<?php
/**
 * @version $Header$
 * 
 * @package blogs
 * @subpackage functions
 */
 
/**
 * Initial Setup
 */
require_once( '../kernel/includes/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'blogs' );

require_once( BLOGS_PKG_INCLUDE_PATH.'lookup_blog_inc.php');

// Now check permissions to access this page
$gContent->verifyViewPermission();

if ( $gBitSystem->isFeatureActive( 'blog_ajax_more' ) && $gBitThemes->isJavascriptEnabled() ){
	$gBitSmarty->assign('ajax_more', TRUE);
	$gBitThemes->loadAjax( 'mochikit', array( 'Iter.js', 'DOM.js', 'Style.js', 'Color.js', 'Position.js', 'Visual.js' ) );
}

// Display the template
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assignByRef( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' , array( 'display_mode' => 'display' ));
?>
