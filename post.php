<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/post.php,v 1.33 2007/03/21 17:29:31 wjames5 Exp $

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
$gBitSystem->verifyPermission( 'p_blogs_post' );

//DEPRECATED -wjames5
//require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');
require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
require_once( BLOGS_PKG_PATH.'BitBlog.php');
$gBlog = new BitBlog();

// nuke post if requested
if( !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'remove' && !empty( $_REQUEST['remove_post_id'] ) ) {
		$tmpPost = new BitBlogPost( $_REQUEST['remove_post_id'] );
		$tmpPost->load();
		if( !$gContent->hasEditPermission() ) {
			$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot remove this post" );
		}
		if( isset( $_REQUEST["confirm"] ) ) {
			if( $tmpPost->expunge() ) {
				header( "Location: ".BLOGS_PKG_URL.'index.php?status_id='.( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' ) );
				die;
			} else {
				$feedback['error'] = $tmpPost->mErrors;
			}
		}
		$gBitSystem->setBrowserTitle( 'Confirm removal of '.$tmpPost->mInfo['title'] );		
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['status_id'] = ( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' );
		$formHash['remove_post_id'] = $_REQUEST['remove_post_id'];
		$msgHash = array(
			'label' => 'Remove Blog Post',
			'confirm_item' => $tmpPost->mInfo['title'],
			'warning' => 'This will remove the above blog post. This cannot be undone.',
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
}


$gContent->invokeServices( 'content_edit_function' );

if (isset($_REQUEST['remove_image'])) {
	$gContent->expungeAttachment( $_REQUEST['remove_image'] );
}

if (isset($_REQUEST["preview"])) {
	$data = $_REQUEST['edit'];

	$parsed_data = $gContent->parseData( $_REQUEST['edit'], (!empty($_REQUEST['format_guid']) ? $_REQUEST['format_guid'] : 'tikiwiki' ));

	// used by the preview page
	/* DEPRECATED - need a substitute for getting a list of the blogs we want to cross post to -wjames5
	$post_info_blog = array($gBlog->getBlog($_REQUEST['blog_id']));
	$post_info = array(
		'title' => isset( $_REQUEST["title"] ) ? $_REQUEST['title'] : '',
		'blogtitle' => isset( $post_info_blog[0]["title"] ) ? $post_info_blog[0]['title'] : '',
		'use_title' => 'y',
		'created' => time(),
	);
	$gBitSmarty->assign('post_info', $post_info);
	*/
	$gBitSmarty->assign('data', $data);
	$gBitSmarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
	$gBitSmarty->assign('parsed_data', $parsed_data);
	$gBitSmarty->assign('preview', 'y');
} elseif (isset($_REQUEST['save_post']) || isset($_REQUEST['save_post_exit'])) {
	$gBitSmarty->assign('individual', 'n');

	if( $gContent->store( $_REQUEST ) ) {
		$postid = $gContent->mPostId;
		$gBitSmarty->assign('post_id', $gContent->mPostId);

		if (isset($_REQUEST['save_post_exit'])) {
			header ("location: ".BLOGS_PKG_URL."view_post.php?post_id=$postid");
			die;
		}

		$data = $_REQUEST['edit'];
		$parsed_data = $gContent->parseData( $_REQUEST['edit'], (!empty($_REQUEST['format_guid']) ? $_REQUEST['format_guid'] : 'tikiwiki' ));

		if( empty( $data ) ) {
			$data = '';
		}

		$gBitSmarty->assign('data', $data);
		$gBitSmarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
		$gBitSmarty->assign('trackbacks_to', explode(',', $_REQUEST['trackback']));
		$gBitSmarty->assign('parsed_data', $parsed_data);

		$gContent->load();
	}
}

// WYSIWYG and Quicktag variable
$gBitSmarty->assign( 'textarea_id', LIBERTY_TEXT_AREA );

/* DEPRECATED -need a replacement for this but this is all kinds of crazy -wjames5
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

/*probably get rid of this stuff too -wjame5
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


/* replace the above with something like this, with perms check on each blog, 
 * I think blog_post.tpl will need a bit of modifying as well 
 * trick is when admin has to check perms on 1000 blogs of legacy sites -wjames5
$blogs = $gBlog->getList();
$gBitSmarty->assign( 'availableBlogs', $blogs );
 */
 
// Need ajax for attachment browser
$gBitSmarty->assign('loadAjax', true);

$gBitSystem->display( 'bitpackage:blogs/blog_post.tpl', "Create Blog Post" );
?>