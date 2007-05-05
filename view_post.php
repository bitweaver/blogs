<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view_post.php,v 1.8 2007/05/05 19:14:20 spiderr Exp $

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


// This is what Articles would do instead of calling display_bitblogpost_inc -wjames5
/*
//$gBitSmarty->assign_by_ref( 'post_info', $gContent->mInfo );

// get all the services that want to display something on this page
$displayHash = array( 'perm_name' => 'p_blogs_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

// Comments engine!
if( @$gContent->mInfo['allow_comments'] == 'y' ) {
	$comments_vars = Array( 'post' );
	$comments_prefix_var='post:';
	$comments_object_var='post';
	$commentsParentId = $gContent->mContentId;
	$comments_return_url = $_SERVER['PHP_SELF']."?post_id=".$_REQUEST['post_id'];
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );
}

// Display the Index Template
$gBitSystem->display( 'bitpackage:blogs/view_blog_post.tpl', @$gContent->mInfo['title'] );
*/

?>
