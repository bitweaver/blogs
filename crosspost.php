<?php
/**
 * @version $Header$
 * @package blogs
 * @subpackage functions
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyPermission( 'p_blogs_admin' );

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
require_once( BLOGS_PKG_PATH.'BitBlog.php');
$gBlog = new BitBlog();

$gBitUser->verifyTicket();

//if crosspost save store it and send us to the post's page
if( isset( $_REQUEST['crosspost_post']) || isset($_REQUEST['save_post_exit'] ) ) {
	$crosspost_note = isset( $_REQUEST['crosspost_note'] )? $_REQUEST['crosspost_note']:NULL;
	if( $gContent->isValid() && $gContent->storePostMap( $gContent->mInfo, $_REQUEST['blog_content_id'], $crosspost_note ) ) {
		$gContent->load();
		bit_redirect( $gContent->getContentUrl() );
	}
}

// nuke crosspost if requested
if( !empty( $_REQUEST['action']) && ($_REQUEST['action'] == 'remove') && $gContent->isValid() ) {
	// confirm first
	if( isset( $_REQUEST["confirm"] ) ) {
		//remove it, then relaod the crossposting form
		if ( $gContent->expungePostMap( $gContent->mInfo['content_id'], array( $_REQUEST["blog_content_id"] ) ) ){
			$gContent->load();
		}else{
			$feedback['error'] = $gContent->mErrors;
		}
	}else{
		$gBitSystem->setBrowserTitle( tra('Confirm removal of') . ' ' . $gContent->getTitle()); // crossposting from Blog \''.'addblognamehere'.'\'' );		
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['post_id'] = $_REQUEST['post_id'];
		$formHash['blog_content_id'] = $_REQUEST['blog_content_id'];
		$msgHash = array(
			'label' => 'Remove Crossposting of Blog Post:',
			'confirm_item' => $gContent->getTitle(),
			'warning' => tra('This will remove the crossposting of the above blog post.'), // from the blog \''.'addblognamehere'.'\'),
			'error' => tra('This cannot be undone!'),
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
}elseif( isset( $_REQUEST["blog_content_id"] )){
	//if we are not removing the post but have received a blog_content_id then we want to edit its note, so load it up
	$crosspost = $gContent->loadPostMap( $gContent->mInfo['content_id'], $_REQUEST["blog_content_id"] );
	$gBitSmarty->assign('crosspost', $crosspost);
	$gBitSmarty->assign('blog_content_id', $_REQUEST['blog_content_id'] );
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

$gBitSystem->display( 'bitpackage:blogs/crosspost.tpl', tra("Crosspost Blog Post") , array( 'display_mode' => 'display' ));
?>