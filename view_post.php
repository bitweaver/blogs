<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view_post.php,v 1.1.1.1.2.5 2005/08/16 04:38:45 spiderr Exp $

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

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

if( $gContent->load() ) {
	include_once( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );
} else {
	$gBitSystem->fatalError( 'Post could not be found.' );
}
?>
