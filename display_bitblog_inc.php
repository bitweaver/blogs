<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/display_bitblog_inc.php,v 1.21 2009/10/01 13:45:31 wjames5 Exp $
 * @package blogs
 * @subpackage functions
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * required setup
 */
$gBitSystem->verifyPackage( 'blogs' );

require_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

$displayHash = array( 'perm_name' => $gContent->mViewContentPerm );
$gContent->invokeServices( 'content_display_function', $displayHash );

if( isset($_REQUEST['user_id']) && !isset( $_REQUEST['blog_id'] ) ) {
	// We will try and grab the first blog owned by the user id given
	$blogsList = $gContent->list_user_blogs($_REQUEST['user_id']);
	if (!empty($blogsList[0]['blog_id'])) {
		$_REQUEST['blog_id'] = $blogsList[0]['blog_id'];
	}
}

if( !$gContent->isValid() ) {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( tra( 'No blog indicated' ));
}

$gContent->verifyViewPermission();

/**
 * i don't think this is in use anymore - xing - Thursday Nov 08, 2007   21:49:22 CET
if( $gContent->getField( 'blog_style' ) && $gBitSystem->getConfig('users_themes') == 'h' ) {
	$gBitThemes->setStyle( $gContent->getField( 'blog_style' ) );
	$gBitThemes->mStyles['styleSheet'] = $gBitSystem->getStyleCss( $gContent->getField( 'blog_style' ), $gContent->getField( 'user_id' ) );
	$gBitSmarty->assign( 'userStyle', $gContent->getField( 'blog_style' ) );
}
 */

if( !$gContent->hasUpdatePermission() ) {
	$gContent->addHit();
}

$now = $gBitSystem->getUTCTime();

$blogPost = new BitBlogPost();
$listHash = array();
$listHash['blog_id'] = $gContent->getField( 'blog_id' );
$listHash['parse_data'] = TRUE;
$listHash['max_records'] = $gContent->getField( 'max_posts' );
$listHash['load_num_comments'] = TRUE;
$blogPosts = $blogPost->getList( $listHash );
if( count( $blogPosts['data'] ) ) {
	// If there're more records then assign next_offset
	$gBitSmarty->assign_by_ref('blogPosts', $blogPosts["data"]);
	$gBitSmarty->assign( 'listInfo', $blogPosts['listInfo'] );
} elseif( $gContent->hasPostPermission() ) {
	bit_redirect( BLOGS_PKG_URL.'post.php?blog_id='.$gContent->getField( 'blog_id' ) );
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

	if ( $watch = $gBitUser->getEventWatches( 'blog_post', $_REQUEST['blog_id'] ) ) {
		$gBitSmarty->assign('user_watching_blog', 'y');
	}
}

$gBitSmarty->assign('descriptionLength', $gBitSystem->getConfig( 'blog_posts_description_length', 500 ) );
$gBitSmarty->assign('showDescriptionsOnly', TRUE);

if ( $gBitSystem->isFeatureActive( 'blog_ajax_more' ) && $gBitThemes->isJavascriptEnabled() ){
	$gBitSmarty->assign('ajax_more', TRUE);
	$gBitThemes->loadAjax( 'mochikit', array( 'Iter.js', 'DOM.js', 'Style.js', 'Color.js', 'Position.js', 'Visual.js' ) );
}
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog.tpl', $gContent->getTitle() , array( 'display_mode' => 'display' ));
?>
