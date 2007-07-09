<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/recent_posts.php,v 1.2 2007/06/22 10:14:52 lsces Exp $
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

require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

$gBitSmarty->assign( 'showEmpty', TRUE );

// Display the template
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );
?>