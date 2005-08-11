<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view_post.php,v 1.5 2005/08/11 13:03:44 squareing Exp $

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

if ( empty( $_REQUEST["post_id"] ) && empty( $_REQUEST["content_id"] ) ) {
	$gBitSystem->fatalError( 'No post indicated' );
}

global $gContent;
$postId = !empty( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : NULL;
$conId = !empty( $_REQUEST['content_id'] ) ? $_REQUEST['content_id'] : NULL;

$gContent = new BitBlogPost( $postId, $conId );
if( $gContent->load() ) {
	// get the pigeonholes info
	if( $gBitSystem->isPackageActive( 'pigeonholes' ) ) {
		include_once( PIGEONHOLES_PKG_PATH.'get_pigeonholes_info_inc.php' );
	}

	include_once( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );
} else {
	$gBitSystem->fatalError( 'Post could not be found.' );
}
?>
