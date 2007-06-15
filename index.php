<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/index.php,v 1.11 2007/06/15 18:53:17 squareing Exp $

 * @package blogss
 * @subpackage functions
 */
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
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
