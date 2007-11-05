<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/recent_posts.php,v 1.6 2007/11/05 21:35:22 wjames5 Exp $
 * 
 * @package blogs
 * @subpackage functions
 */
 
/**
 * Initial Setup
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'blogs' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_blogs_view' );

if ( $gBitSystem->isFeatureActive( 'blog_ajax_more' ) ){
	$gBitThemes->loadAjax( 'mochikit' );
}

// Display the template
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );
?>
