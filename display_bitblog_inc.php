<?php
/**
 * @package blogs
 * @subpackage functions
 * 
 * Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 * required setup
 */
require_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

$displayHash = array( 'perm_name' => 'p_blogs_view' );
$gBlog->invokeServices( 'content_display_function', $displayHash );

$gBitSystem->verifyPackage( 'blogs' );

if( isset($_REQUEST['user_id']) && !isset( $_REQUEST['blog_id'] ) ) {
	// We will try and grab the first blog owned by the user id given
	$blogsList = $gBlog->list_user_blogs($_REQUEST['user_id']);
	if (!empty($blogsList[0]['blog_id'])) {
		$_REQUEST['blog_id'] = $blogsList[0]['blog_id'];
	}
}

if (!isset($_REQUEST["blog_id"])) {
	$gBitSystem->fatalError( 'No blog indicated' );
}

$gBitSystem->verifyPermission( 'p_blogs_view' );

if( $gBlog->getField( 'blog_style' ) && $gBitSystem->getConfig('users_themes') == 'h' ) {
	$gBitSystem->setStyle( $gBlog->getField( 'blog_style' ) );
	$gBitSystem->mStyles['styleSheet'] = $gBitSystem->getStyleCss( $gBlog->getField( 'blog_style' ), $gBlog->getField( 'user_id' ) );
	$gBitSmarty->assign( 'userStyle', $gBlog->getField( 'blog_style' ) );
}

if( !$gBlog->hasEditPermission() ) {
	$gBlog->addHit();
}


/* MOVED TO POST
if (isset($_REQUEST["remove"])) {
	$blogPost = new BitBlogPost( $_REQUEST["remove"] );
	if( $blogPost->load() ) {
		if( !$blogPost->hasEditPermission() ) {
			$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot remove this post" );
		}

		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['remove'] = $_REQUEST['remove'];
			$formHash['blog_id'] = $_REQUEST['blog_id'];
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to remove post '.$_REQUEST['remove'].'?' ) );
		} else {
			$blogPost->expunge();
			bit_redirect( BLOGS_PKG_URL );
		}
	}
}
*/


$now = $gBitSystem->getUTCTime();

$blogPost = new BitBlogPost();
$listHash = array();
$listHash['blog_id'] = $_REQUEST['blog_id'];
$listHash['parse_data'] = TRUE;
$listHash['max_records'] = $gBlog->getField( 'max_posts' );
$listHash['load_num_comments'] = TRUE;
$blogPosts = $blogPost->getList( $listHash );
if( count( $blogPosts['data'] ) ) {
	// If there're more records then assign next_offset
	$gBitSmarty->assign_by_ref('blogPosts', $blogPosts["data"]);
} elseif( $gBlog->hasPostPermission() ) {
	bit_redirect( BLOGS_PKG_URL.'post.php?blog_id='.$gBlog->getField( 'blog_id' ) );
}

if( $gBitSystem->isFeatureActive( 'users_watches' ) ) {
	if( $gBitUser->isValid() && isset( $_REQUEST['watch_event'] ) ) {
		if ($_REQUEST['watch_action'] == 'add') {
			$blogPost = new BitBlogPost( $_REQUEST['watch_object'] );
			if( $blogPost->load() ) {
				$gBitUser->storeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'], tra('blog'), $blogPost->mInfo['title'], $blogPost->getDisplayUrl() );
			}
		} else {
			$gBitUser->expungeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'] );
		}
	}

	$gBitSmarty->assign('user_watching_blog', 'n');

	if ( $watch = $gBitUser->getEventWatches( $gBitUser->mUserId, 'blog_post', $_REQUEST['blog_id'])) {
		$gBitSmarty->assign('user_watching_blog', 'y');
	}
}

$gBitSmarty->assign('descriptionLength', $gBitSystem->getConfig( 'blog_posts_description_length', 500 ) );
$gBitSmarty->assign('showDescriptionsOnly', TRUE);
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog.tpl', $gBlog->getTitle() );

?>
