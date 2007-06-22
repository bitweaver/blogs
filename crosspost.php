<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/crosspost.php,v 1.3 2007/06/22 09:05:13 lsces Exp $
 * @package blogs
 * @subpackage functions
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyPermission( 'p_blogs_admin' );

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
require_once( BLOGS_PKG_PATH.'BitBlog.php');
$gBlog = new BitBlog();


if( isset( $_REQUEST['crosspost_post']) || isset($_REQUEST['save_post_exit'] ) ) {
	if( $gContent->isValid() && $gContent->storePostMap( $gContent->getField( 'content_id' ), $_REQUEST['blog_content_id'] ) ) {
		$gContent->load();
		bit_redirect( $gContent->getDisplayUrl() );
	}
}


$post_id = $gContent->mPostId;
$gBitSmarty->assign('post_id', $gContent->mPostId );
$parsed_data = $gContent->parseData();
$gBitSmarty->assign('parsed_data', $parsed_data);
$gBitSmarty->assign('post_info', $gContent->mInfo );


// Get List of available blogs
$listHash = array();
$listHash['sort_mode'] = 'title_desc';
if( !$gBitUser->hasPermission( 'p_blogs_admin' )) {
	$blogs = $gBlog->getList( $listHash );
	$listHash['user_id'] = $gBitUser->mUserId;
	$listHash['content_perm_name'] = 'p_blogs_post';
}
$blogs = $gBlog->getList( $listHash );
$availableBlogs = array();
foreach( array_keys( $blogs ) as $blogContentId ) {
	$availableBlogs[$blogContentId] = $blogs[$blogContentId]['title'];
}
$gBitSmarty->assign( 'availableBlogs', $availableBlogs );

$gBitSmarty->assign_by_ref('blogs', $blogs['data']);
if (isset($_REQUEST['blog_content_id'])) {
	$gBitSmarty->assign('blog_content_id', $_REQUEST['blog_content_id'] );
}

$gBitSystem->display( 'bitpackage:blogs/crosspost.tpl', "Crosspost Blog Post" );
?>