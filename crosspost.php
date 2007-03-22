<?php
/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyPermission( 'p_blogs_admin' );

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
require_once( BLOGS_PKG_PATH.'BitBlog.php');
$gBlog = new BitBlog();

if (isset($_REQUEST['crosspost_post']) || isset($_REQUEST['save_post_exit'])) {
	$gBitSmarty->assign('individual', 'n');
	if( $gContent->storePostMap( $gContent->mPostId, $_REQUEST['blog_content_id'] ) ) {
		$gContent->load();
		$postid = $gContent->mPostId;
		header ("location: ".BLOGS_PKG_URL."view_post.php?post_id=$postid");
		die;
	}
}


$post_id = $gContent->mPostId;
$gBitSmarty->assign('post_id', $gContent->mPostId );
$parsed_data = $gContent->parseData();
$gBitSmarty->assign('parsed_data', $parsed_data);
$gBitSmarty->assign('post_info', $gContent->mInfo );


/* DEPRECATED -need a replacement for this match what is done in post.php when complete -wjames5
 * possible solution at end of commented parts
 */
// $blogs holds a list of blogs which the user can post into
// If a specific blog_id is passed in, we will use that and not load up all the blogs
if ($gBitUser->hasPermission( 'p_blogs_admin' )) {
	$listHash = array();
	$listHash['sort_mode'] = 'created_desc';
	$blogs = $gBlog->getList( $listHash );
	// Get blogs the admin owns
	$listHash = array();
	$listHash['user_id'] = $gBitUser->mUserId;
	$adminBlogs = $gBlog->getList( $listHash );
} else {
	if ( $gBlog->isValid() ) {
		if( $gBlog->hasPostPermission() ) {
			$blogs['data'][] = $gBlog->mInfo;
		} else {
			$gBitSystem->fatalError( tra("You cannot post into this blog") );
		}
	} else {
		$listHash = array();
		$listHash['user_id'] = $gBitUser->mUserId;
		$blogs = $gBlog->getList( $listHash );
	}
}

/* DEPRECATED -need a replacement for this match what is done in post.php when complete -wjames5
 */
$availableBlogs = array();
foreach( array_keys( $blogs ) as $blogContentId ) {
	$availableBlogs[$blogContentId] = $blogs[$blogContentId]['title'];
}
$gBitSmarty->assign( 'availableBlogs', $availableBlogs );

$gBitSmarty->assign_by_ref('blogs', $blogs['data']);
if (isset($_REQUEST['blog_content_id'])) {
	$gBitSmarty->assign('blog_content_id', $_REQUEST['blog_content_id'] );
}
 
// Need ajax for attachment browser
$gBitSmarty->assign('loadAjax', true);

$gBitSystem->display( 'bitpackage:blogs/crosspost.tpl', "Crosspost Blog Post" );
?>