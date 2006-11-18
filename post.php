<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/post.php,v 1.21 2006/11/18 21:43:16 spiderr Exp $

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

include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$gBitSystem->verifyPackage( 'blogs' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_blogs_post' );

include_once( LIBERTY_PKG_PATH.'edit_help_inc.php' );

if (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y') {
	$gBitSmarty->assign('wysiwyg', 'y');
}

// $blogs holds a list of blogs which the user can post into
// If a specific blog_id is passed in, we will use that and not load up all the blogs
if ($gBitUser->hasPermission( 'p_blogs_admin' )) {
	$listHash = array();
	$listHash['sort_mode'] = 'created_desc';
	$blogs_temp = $gBlog->getList( $listHash );
	$blogs = $blogs_temp['data'];
	// Get blogs the admin owns
	$listHash = array();
	$listHash['user_id'] = $gBitUser->mUserId;
	$adminBlogs = $gBlog->getList( $listHash );
	if( !empty( $adminBlogs['data'] ) ) {
		// Use one of these as the default blog to post into			
		$blog_id = $adminBlogs['data'][0]['blog_id'];
	}
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

if( empty( $_REQUEST['blog_id'] ) && count($blogs) >  0 ) {
	$_REQUEST['blog_id'] = $blogs['data'][0]['blog_id'];	// Default to the first blog returned that this user owns
}

if( count($blogs) == 0 ) {
	if( $gBitUser->hasPermission( 'p_blogs_create' )) {
		$mid = 'bitpackage:blogs/edit_blog.tpl';
		$gBitSmarty->assign('warning', tra("Before you can post, you first need to create a blog that will hold your posts."));
	} else {
		$gBitSmarty->assign('msg', tra("You can't post in any blog maybe you have to create a blog first"));
		$mid = 'error.tpl';
	}

} else {
	$mid = 'bitpackage:blogs/blog_post.tpl';
}

$gBitSmarty->assign('data', '');
$gBitSmarty->assign('created', $gBitSystem->getUTCTime());

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

$gContent->invokeServices( 'content_edit_function' );


if (isset($_REQUEST['remove_image'])) {

	$gContent->expungeAttachment( $_REQUEST['remove_image'] );
}

// If the post_id is passed then get the article data
if( isset($_REQUEST["post_id"]) && $_REQUEST["post_id"] > 0 ) {
	$gContent->load();

	if( $gContent->mInfo["user_id"] != $gBitUser->mUserId || !$gBitUser->isValid() ) {
		$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot edit this blog" );
	}

	$gBitSmarty->assign('data', $gContent->mInfo["data"]);
	$gBitSmarty->assign('title', $gContent->mInfo["title"]);
	$gBitSmarty->assign('trackbacks_to', $gContent->mInfo["trackbacks_to"]);
	$gBitSmarty->assign('created', $gContent->mInfo["created"]);
	$gBitSmarty->assign('parsed_data', $gContent->parseData() );
} else {
	// Avoid undefined trackbacks_to smarty var in the case of 'preview'
	$gBitSmarty->assign('trackbacks_to', NULL);
}

if (isset($_REQUEST["preview"])) {
	$data = $_REQUEST['edit'];

	$parsed_data = $gContent->parseData( $_REQUEST['edit'], (!empty($_REQUEST['format_guid']) ? $_REQUEST['format_guid'] : 'tikiwiki' ));

	// used by the preview page
	$post_info_blog = array($gBlog->getBlog($_REQUEST['blog_id']));
	$post_info = array(
		'title' => isset( $_REQUEST["title"] ) ? $_REQUEST['title'] : '',
		'blogtitle' => isset( $post_info_blog[0]["title"] ) ? $post_info_blog[0]['title'] : '',
		'use_title' => 'y',
		'created' => time(),
	);
	$gBitSmarty->assign('post_info', $post_info);
	$gBitSmarty->assign('data', $data);
	$gBitSmarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
	$gBitSmarty->assign('parsed_data', $parsed_data);
	$gBitSmarty->assign('preview', 'y');
} elseif (isset($_REQUEST['save_post']) || isset($_REQUEST['save_post_exit'])) {
	$gBitSmarty->assign('individual', 'n');

	if ($gBitUser->object_has_one_permission($_REQUEST["blog_id"], 'blog')) {
		$gBitSmarty->assign('individual', 'y');

		if (!$gBitUser->isAdmin()) {
			// Now get all the permissions that are set for this content type
			$perms = $gBitUser->getPermissions('', 'blogs');
			foreach( array_keys( $perms ) as $permName ) {
				if ($gBitUser->object_has_permission( $user, $_REQUEST["blog_id"], 'blog', $permName ) ) {
					$$permName = 'y';
					$gBitSmarty->assign( $permName, 'y');
				} else {
					$$permName = 'n';
					$gBitSmarty->assign( $permName, 'n');
				}
			}
		}
	}

	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';

	if( !isset( $_REQUEST['trackback'] ) ) { $_REQUEST['trackback'] = ''; }

	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$_REQUEST['upload'] = &$_FILES['userfile1'];
		$_REQUEST['upload']['process_storage'] = STORAGE_IMAGE;
	}

	if( $gContent->store( $_REQUEST ) ) {
		$postid = $_REQUEST['post_id'];
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

if (isset($_REQUEST["post_id"])) {
	$post_id = $_REQUEST["post_id"];
} else {
	$post_id = NULL;
}
$gBitSmarty->assign_by_ref('post_id', $post_id);

$gBitSmarty->assign_by_ref('post_images', $gContent->mStorage);

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'blog_id',
	'post_id'
);

$gBitSmarty->assign_by_ref('blogs', $blogs['data']);
$gBitSmarty->assign('blog_id', $_REQUEST['blog_id'] );

$gBitSystem->setBrowserTitle("Create Blog Post");
// Display the Index Template
$gBitSystem->display( $mid );
$gBitSmarty->assign('show_page_bar', 'n');

?>
