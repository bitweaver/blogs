<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view_post.php,v 1.9 2007/05/16 16:47:07 wjames5 Exp $

 * @package blogs
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

require_once( BLOGS_PKG_PATH.'BitBlogPost.php' );

$gBitSystem->verifyPackage( 'blogs' );

if( !$gBitUser->hasPermission( 'p_blogs_view' ) ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot view this section" ) );
	$gBitSystem->display( "error.tpl" );
	die;
} elseif( !isset( $_REQUEST["post_id"] ) && !isset( $_REQUEST["content_id"] ) ) {
	$gBitSmarty->assign( 'msg', tra( "No blog post indicated" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

include_once( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );

if( $gContent->isValid() ) {
	$gContent->addHit();
}
?>
