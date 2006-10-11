<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/edit.php,v 1.11 2006/10/11 06:05:12 spiderr Exp $
 * @package blogs
 * @subpackage functions
 */
//

// @package blogs

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

$gBitSystem->verifyPackage( 'blogs' );

if (isset($_REQUEST["blog_id"])) {
	$blog_id = $_REQUEST["blog_id"];
} else {
	$blog_id = 0;
}

$gBitSmarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($blog_id, 'blog')) {
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

$gBitSmarty->assign('blog_id', $blog_id);
$gBitSmarty->assign('title', '');
$gBitSmarty->assign('description', '');
$gBitSmarty->assign('public_blog', 'n');
$gBitSmarty->assign('use_find', 'y');
$gBitSmarty->assign('use_title', 'y');
$gBitSmarty->assign('allow_comments', 'y');
$gBitSmarty->assign('max_posts', 10);


if (!isset($created)) {
	$created=time();
	$gBitSmarty->assign('created', $created);
}

if (!isset($last_modified)) {
	$last_modified=time();
	$gBitSmarty->assign('last_modified', $last_modified);
}

if (isset($_REQUEST["heading"]) && $gBitUser->hasPermission( 'bit_p_edit_templates' ) ) {
	$heading = $_REQUEST["heading"];
} else {
	$heading = '';
}

$gBitSmarty->assign_by_ref('heading', $heading);

if (isset($_REQUEST["blog_id"]) && $_REQUEST["blog_id"] > 0) {
	// Check permission
	$data = $gBlog->get_blog($_REQUEST["blog_id"]);

	if ($data["user_id"] != $gBitUser->mUserId || !$gBitUser->mUserId) {
		$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot edit this blog" );
	}

	$gBitSmarty->assign('title', $data["title"]);
	$gBitSmarty->assign('description', $data["description"]);
	$gBitSmarty->assign('public_blog', $data["public_blog"]);
	$gBitSmarty->assign('use_title', $data["use_title"]);
	$gBitSmarty->assign('allow_comments', $data["allow_comments"]);
	$gBitSmarty->assign('use_find', $data["use_find"]);
	$gBitSmarty->assign('max_posts', $data["max_posts"]);
	$gBitSmarty->assign('heading', $data["heading"]);
} else {
	$data = NULL;
}

// Now check permissions to access this page
if (!$gBitUser->hasPermission( 'p_blogs_create' ) && ($gBitUser->mUserId != $data['user_id'] || !$gBitUser->mUserId) ) {
	$gBitSystem->fatalPermission('p_blog_create');
}

if (isset($_REQUEST['preview'])) {
	$gBitSmarty->assign('title', $_REQUEST["title"]);

	$gBitSmarty->assign('description', $_REQUEST["description"]);
	$gBitSmarty->assign('public_blog', isset($_REQUEST["public_blog"]) ? 'y' : 'n');
	$gBitSmarty->assign('use_find', isset($_REQUEST["use_find"]) ? 'y' : 'n');
	$gBitSmarty->assign('use_title', isset($_REQUEST["use_title"]) ? 'y' : 'n');
	$gBitSmarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$gBitSmarty->assign('max_posts', $_REQUEST["max_posts"]);
	$gBitSmarty->assign('heading', $heading);
}

if (isset($_REQUEST['save_blog'])) {

	if (isset($_REQUEST["public_blog"]) && $_REQUEST["public_blog"] == 'on') {
		$public = 'y';
	} else {
		$public = 'n';
	}

	$use_title = isset($_REQUEST['use_title']) ? 'y' : 'n';
	$allow_comments = isset($_REQUEST['allow_comments']) ? 'y' : 'n';
	$use_find = isset($_REQUEST['use_find']) ? 'y' : 'n';
	$heading = isset($_REQUEST['heading']) ? $_REQUEST['heading'] : '';

	if( $gBlog->store( $_REQUEST ) ) {
		bit_redirect( $gBlog->getDisplayUrl() );
	} else {
vd( $this->mErrors );
		$gBitSmarty->assign_by_ref( 'errors', $gBlog->mErrors );
	}
}

$gBitSystem->setBrowserTitle("Edit Blog Post - ".$data['title']);
// Display the Index Template
$gBitSmarty->assign('show_page_bar', 'n');
$gBitSystem->display( 'bitpackage:blogs/edit_blog.tpl');

?>
