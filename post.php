<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/post.php,v 1.1.1.1.2.9 2005/08/10 15:40:36 squareing Exp $

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
$gBitSystem->verifyPermission( 'bit_p_blog_post' );

include_once( LIBERTY_PKG_PATH.'edit_help_inc.php' );

if (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y') {
	$gBitSmarty->assign('wysiwyg', 'y');
}

if (isset($_REQUEST["blog_id"])) {
	$blog_id = $_REQUEST["blog_id"];
	$blog_data = $gBlog->get_blog($blog_id);
} else {
	$blog_id = NULL;
}

// $blogs holds a list of blogs which the user can post into
// If a specific blog_id is passed in, we will use that and not load up all the blogs
if ($gBitUser->hasPermission( 'bit_p_blog_admin' )) {
	if ($blog_id) {
		$blogs = array($gBlog->get_blog($blog_id));
	} else {
		$blogs_temp = $gBlog->list_blogs(0, -1, 'created_desc', '');
		$blogs = $blogs_temp['data'];
		// Get blogs the admin owns
		$adminBlogs = $gBlog->list_user_blogs($gBitUser->mUserId);
		if (count($adminBlogs) > 0) {
			// Use one of these as the default blog to post into
			$blog_id = $adminBlogs[0]['blog_id'];
		}
	}
} else {
	if ($blog_id) {
		$blogInfo = $gBlog->get_blog($blog_id);
		if ($blogInfo) {
			//if (($blogInfo['user_id'] != $gBitUser->mUserId && $blogInfo['public'] != 'y') && !$gBlog->viewerCanPostIntoBlog()) {
			if ($gBlog->viewerCanPostIntoBlog()) {
				$gBitSmarty->assign('msg', tra("You cannot post into this blog"));
				$gBitSystem->display('error.tpl');
				die();
			}
			$blogs = array($blogInfo);
		} else {
			$gBitSmarty->assign('msg',tra("The given blog does not exist"));
			$gBitSystem->display('error.tpl');
			die();
		}
	} else {
		$blogs = $gBlog->list_user_blogs($gBitUser->mUserId, 1);
	}
}

if (!$blog_id && count($blogs) > 0) {
		$blog_id = $blogs[0]['blog_id'];	// Default to the first blog returned that this user owns
}
if (count($blogs) == 0) {
	if( $gBitUser->hasPermission( 'bit_p_create_blogs' )) {
		$mid = 'bitpackage:blogs/edit_blog.tpl';
		$gBitSmarty->assign('warning', tra("Before you can post, you first need to create a blog that will hold your posts."));
	} else {
		$gBitSmarty->assign('msg', tra("You can't post in any blog maybe you have to create a blog first"));
		$mid = 'error.tpl';
	}

} else {
	if ($gBitSystem->getPreference('package_quicktags','n') == 'y') {
	  include_once( QUICKTAGS_PKG_PATH.'quicktags_inc.php' );
	}
	$mid = 'bitpackage:blogs/blog_post.tpl';
}

$gBitSmarty->assign('data', '');
$gBitSmarty->assign('created', date("U"));

$blog_data = $gBlog->get_blog($blog_id);
$gBitSmarty->assign_by_ref('blog_data', $blog_data);

require_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
if ( $gBitSystem->isPackageActive('categories') ) {
	$cat_type = BITBLOGPOST_CONTENT_TYPE_GUID;
	$cat_objid = $gContent->mPostId;
	include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
}


if (isset($_REQUEST['remove_image'])) {

	$gContent->expungeAttachment( $_REQUEST['remove_image'] );
}

// If the post_id is passed then get the article data
if( isset($_REQUEST["post_id"]) && $_REQUEST["post_id"] > 0 ) {
	$gContent->load();

	if( $gContent->mInfo["user_id"] != $gBitUser->mUserId || !$gBitUser->isValid() ) {
		$gBitSystem->verifyPermission( 'bit_p_blog_admin', "Permission denied you cannot edit this blog" );
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

	if ($blog_spellcheck == 'y') {
		if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
			$parsed_data = $gBitSystem->spellcheckreplace($data, $parsed_data, $language, 'blogedit');

			$gBitSmarty->assign('spellcheck', 'y');
		} else {
			$gBitSmarty->assign('spellcheck', 'n');
		}
	}

	// used by the preview page
	$post_info_blog = array($gBlog->get_blog($_REQUEST['blog_id']));
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

	// get files from all packages that process this data further
	foreach( $gBitSystem->getPackageIntegrationFiles( 'form_processor_inc.php', TRUE ) as $package => $file ) {
		if( $gBitSystem->isPackageActive( $package ) ) {
			include_once( $file );
		}
	}
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

	if ($gBitUser->hasPermission( 'bit_p_blog_admin' )) {
		$bit_p_create_blogs = 'y';

		$gBitSmarty->assign('bit_p_create_blogs', 'y');
		$bit_p_blog_post = 'y';
		$gBitSmarty->assign('bit_p_blog_post', 'y');
		$bit_p_read_blog = 'y';
		$gBitSmarty->assign('bit_p_read_blog', 'y');
	}

	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';

	if( !isset( $_REQUEST['trackback'] ) ) { $_REQUEST['trackback'] = ''; }

	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$_REQUEST['upload'] = &$_FILES['userfile1'];
		$_REQUEST['upload']['process_storage'] = STORAGE_IMAGE;
	}

	if( $gContent->store( $_REQUEST ) ) {
		if ( $gBitSystem->isPackageActive('categories') ) {
			$cat_desc = $gLibertySystem->mContentTypes[BITBLOGPOST_CONTENT_TYPE_GUID]['content_description'].' by '.$gBitUser->getDisplayName( FALSE, array( 'real_name' => $gContent->mInfo['real_name'], 'user' => $gContent->mInfo['user'], 'user_id'=>$gContent->mInfo['user_id'] ) );
			$cat_name = $gContent->getTitle();
			$cat_href = $gContent->getDisplayUrl();
			$cat_objid = $gContent->mContentId;
			include_once( CATEGORIES_PKG_PATH.'categorize_inc.php' );
		}
		$postid = $_REQUEST['post_id'];
		$gBitSmarty->assign('post_id', $gContent->mPostId);

		// get files from all packages that process this data further
		foreach( $gBitSystem->getPackageIntegrationFiles( 'form_processor_inc.php', TRUE ) as $package => $file ) {
			if( $gBitSystem->isPackageActive( $package ) ) {
				include_once( $file );
			}
		}

		if (isset($_REQUEST['save_post_exit'])) {
			header ("location: ".BLOGS_PKG_URL."view_post.php?post_id=$postid");
			die;
		}

		$data = $_REQUEST['edit'];
		$parsed_data = $gContent->parseData($_REQUEST['edit']);

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
$gBitSmarty->assign( 'textarea_id', 'editblog' );

// get files from all packages that process this data further
foreach( $gBitSystem->getPackageIntegrationFiles( 'get_form_info_inc.php', TRUE ) as $package => $file ) {
	if( $gBitSystem->isPackageActive( $package ) ) {
		include_once( $file );
	}
}

// assign the integration template files
$gBitSmarty->assign( 'integrationFiles', $gBitSystem->getPackageIntegrationFiles( 'templates/form_info_inc.tpl', TRUE ) );

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

$gBitSmarty->assign_by_ref('blogs', $blogs);
$gBitSmarty->assign('blog_id', $blog_id);
$section = 'blogs';

$gBitSystem->setBrowserTitle("Create Blog Post");
// Display the Index Template
$gBitSystem->display( $mid );
$gBitSmarty->assign('show_page_bar', 'n');

?>
