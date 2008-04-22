<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/post.php,v 1.55 2008/04/22 21:30:22 spiderr Exp $

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

$gBitSystem->verifyPackage( 'blogs' );


require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
require_once( BLOGS_PKG_PATH.'BitBlog.php');

if ( isset( $_REQUEST["blog_id"] ) ) {
	#setup so we know what the default target blog is in the template
	$gBlog = new BitBlog($_REQUEST["blog_id"]);
	$gBlog->load();
	$gBitSmarty->assign('default_target_blog_content_id',$gBlog->mContentId );
	}
else {
	$gBlog = new BitBlog();
	}	 
//must be owner or admin to edit an existing post
if( $gContent->isValid() ) {
	$gContent->verifyEditPermission();
} else {
	$gBitSystem->verifyPermission( 'p_blogs_post' );
}

// Editing page needs general ticket verification
$gBitUser->verifyTicket();

// nuke post if requested
if( !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'remove' && $gContent->isValid() ) {
		if( isset( $_REQUEST["confirm"] ) ) {
			$redirect = !empty( $gContent->mInfo['blogs'] ) ? BLOGS_PKG_URL.'view.php?content_id='.key( $gContent->mInfo['blogs'] ) : BLOGS_PKG_URL;
			if( $gContent->expunge() ) {
				bit_redirect( $redirect );
			} else {
				$feedback['error'] = $gContent->mErrors;
			}
		}
		$gBitSystem->setBrowserTitle( 'Confirm removal of '.$gContent->getTitle() );		
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['post_id'] = $_REQUEST['post_id'];
		$msgHash = array(
			'label' => 'Remove Blog Post',
			'confirm_item' => $gContent->getTitle(),
			'warning' => 'This will remove the above blog post. This cannot be undone.',
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
}

if (isset($_REQUEST['remove_image'])) {
	$gContent->expungeAttachment( $_REQUEST['remove_image'] );
}

if( isset( $_REQUEST['format_guid'] ) && !isset( $gContent->mInfo['format_guid'] ) ) {
	$formInfo['format_guid'] = $gContent->mInfo['format_guid'] = $_REQUEST['format_guid']; 
}

if (isset($_REQUEST["preview"])) {
	$post = $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
	$gContent->invokeServices( 'content_preview_function' );
	$gBitSmarty->assign_by_ref( 'post_info', $post );
	/* minor hack to accomodate the view_blog_post.tpl
	 * this can eventually be removed with a change to the tpl to use post_info['parsed_data'] 
	 * but requires clean up in a few places.
	 */
	$gBitSmarty->assign('parsed_data', $post['parsed_data']);	
} elseif (isset($_REQUEST['save_post']) || isset($_REQUEST['save_post_exit'])) {
	if( $gContent->store( $_REQUEST ) ) {
		$postid = $gContent->mPostId;
		$gBitSmarty->assign('post_id', $gContent->mPostId);

		if (isset($_REQUEST['save_post_exit'])) {
			header ("location: ".BLOGS_PKG_URL."view_post.php?post_id=$postid");
			die;
		}
		
		$parsed_data = $gContent->parseData( $gContent->getField('data'), ($gContent->getField('format_guid') ? $gContent->getField('format_guid') : 'tikiwiki') );

		$gBitSmarty->assign( 'title', $gContent->getTitle('title') );
		$gBitSmarty->assign( 'trackbacks_to', explode(',', $gContent->getField('trackbacks_to')) );
		$gBitSmarty->assign( 'parsed_data', $parsed_data );
	}
} else {
	$gContent->invokeServices( 'content_edit_function' );
	$gBitSmarty->assign_by_ref('post_info', $gContent->mInfo);
}

// Get List of available blogs
$listHash = array();
$listHash['sort_mode'] = 'title_asc';
$listHash['max_records'] = MAX_RECORDS;
if( !$gBitUser->hasPermission( 'p_blogs_admin' )) {
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

$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );

$gBitSmarty->assign( 'textarea_label', 'Post Content' );

// tweak title displayed for better usuability in browser history
$gBitSystem->display( 'bitpackage:blogs/blog_post.tpl', $gContent->isValid() ? tra( "Edit Blog Post" ).": ".$gContent->getTitle() : tra( "Create Blog Post" ) );
?>
