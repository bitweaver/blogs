<?php

// $Header: /cvsroot/bitweaver/_bit_blogs/edit.php,v 1.1 2005/06/19 03:57:42 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

$gBitSystem->verifyPackage( 'blogs' );

if (isset($_REQUEST["blog_id"])) {
	$blog_id = $_REQUEST["blog_id"];
} else {
	$blog_id = 0;
}

$smarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($blog_id, 'blog')) {
	$smarty->assign('individual', 'y');

	if (!$gBitUser->isAdmin()) {
		// Now get all the permissions that are set for this content type
		$perms = $gBitUser->getPermissions('', 'blogs');
		foreach( array_keys( $perms ) as $permName ) {
			if ($gBitUser->object_has_permission( $user, $_REQUEST["blog_id"], 'blog', $permName ) ) {
				$$permName = 'y';
				$smarty->assign( $permName, 'y');
			} else {
				$$permName = 'n';
				$smarty->assign( $permName, 'n');
			}
		}
	}
}

$smarty->assign('blog_id', $blog_id);
$smarty->assign('title', '');
$smarty->assign('description', '');
$smarty->assign('public', 'n');
$smarty->assign('use_find', 'y');
$smarty->assign('use_title', 'y');
$smarty->assign('allow_comments', 'y');
$smarty->assign('max_posts', 10);


if (!isset($created)) {
	$created=time();
	$smarty->assign('created', $created);
}

if (!isset($last_modified)) {
	$last_modified=time();
	$smarty->assign('last_modified', $last_modified);
}

if (isset($_REQUEST["heading"])and $bit_p_edit_templates) {
	$heading = $_REQUEST["heading"];
} else {
	$heading = '';
}

$smarty->assign_by_ref('heading', $heading);

if (isset($_REQUEST["blog_id"]) && $_REQUEST["blog_id"] > 0) {
	// Check permission
	$data = $gBlog->get_blog($_REQUEST["blog_id"]);

	if ($data["user_id"] != $gBitUser->mUserId || !$gBitUser->mUserId) {
		$gBitSystem->verifyPermission( 'bit_p_blog_admin', "Permission denied you cannot edit this blog" );
	}

	$smarty->assign('title', $data["title"]);
	$smarty->assign('description', $data["description"]);
	$smarty->assign('public', $data["public"]);
	$smarty->assign('use_title', $data["use_title"]);
	$smarty->assign('allow_comments', $data["allow_comments"]);
	$smarty->assign('use_find', $data["use_find"]);
	$smarty->assign('max_posts', $data["max_posts"]);
	$smarty->assign('heading', $data["heading"]);
} else {
	$data = NULL;
}

// Now check permissions to access this page
if (!$gBitUser->hasPermission( 'bit_p_create_blogs' ) && ($gBitUser->mUserId != $data['user_id'] || !$gBitUser->mUserId) ) {
	$smarty->assign('msg', tra("Permission denied you cannot create or edit blogs"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (isset($_REQUEST['preview'])) {
	$smarty->assign('title', $_REQUEST["title"]);

	$smarty->assign('description', $_REQUEST["description"]);
	$smarty->assign('public', isset($_REQUEST["public"]) ? 'y' : 'n');
	$smarty->assign('use_find', isset($_REQUEST["use_find"]) ? 'y' : 'n');
	$smarty->assign('use_title', isset($_REQUEST["use_title"]) ? 'y' : 'n');
	$smarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$smarty->assign('max_posts', $_REQUEST["max_posts"]);
	$smarty->assign('heading', $heading);
}

if (isset($_REQUEST['save_blog'])) {
	
	if (isset($_REQUEST["public"]) && $_REQUEST["public"] == 'on') {
		$public = 'y';
	} else {
		$public = 'n';
	}

	$use_title = isset($_REQUEST['use_title']) ? 'y' : 'n';
	$allow_comments = isset($_REQUEST['allow_comments']) ? 'y' : 'n';
	$use_find = isset($_REQUEST['use_find']) ? 'y' : 'n';
	$heading = isset($_REQUEST['heading']) ? $_REQUEST['heading'] : '';

	$bid = $gBlog->replace_blog($_REQUEST["title"],
	    $_REQUEST["description"], $gBitUser->mUserId, $public,
	    $_REQUEST["max_posts"], $_REQUEST["blog_id"],
	    $heading, $use_title, $use_find,
	    $allow_comments);

	$cat_type = 'blog';
	$cat_objid = $bid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = BitBlog::getBlogUrl( $cat_objid );
	if ($gBitSystem->isPackageActive( 'categories' )) {
		include_once( CATEGORIES_PKG_PATH.'categorize_inc.php' );
	}

	header ("location: ".BLOGS_PKG_URL.'post.php?blog_id='.$bid );
	die;
}

$cat_type = 'blog';
$cat_objid = $blog_id;
if ($gBitSystem->isPackageActive( 'categories' )) {
	include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
}


$gBitSystem->setBrowserTitle("Edit Blog Post - ".$data['title']);
// Display the Index Template
$smarty->assign('show_page_bar', 'n');
$gBitSystem->display( 'bitpackage:blogs/edit_blog.tpl');

?>
