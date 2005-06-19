<?php

// $Header: /cvsroot/bitweaver/_bit_blogs/print_blog_post.php,v 1.1 2005/06/19 03:57:42 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$gBitSystem->verifyPackage( 'blogs' );

if (!isset($_REQUEST["post_id"])) {
	$gBitSystem->fatalError( 'No post indicated' );
}

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

$smarty->assign('post_info', $gContent->mInfo );

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mInfo['post_id'];
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mInfo['post_id'];
$smarty->assign('uri', $uri);
$smarty->assign('uri2', $uri2);

if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

$smarty->assign('offset', $_REQUEST["offset"]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];

$smarty->assign( 'parsed_data', $gContent->parseData() );

$smarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($gContent->mInfo['blog_id'], 'blog')) {
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

if ($gBitUser->hasPermission( 'bit_p_blog_admin' )) {
	$bit_p_create_blogs = 'y';

	$smarty->assign('bit_p_create_blogs', 'y');
	$bit_p_blog_post = 'y';
	$smarty->assign('bit_p_blog_post', 'y');
	$bit_p_read_blog = 'y';
	$smarty->assign('bit_p_read_blog', 'y');
}

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

$ownsblog = 'n';

if ($gBitUser->mUserId && $gBitUser->mUserId == $gContent->mInfo['blog_user_id'] ) {
	$ownsblog = 'y';
}

$smarty->assign('ownsblog', $ownsblog);

if ($feature_theme_control == 'y') {
	$cat_type = 'blog';
	$cat_objid = $gContent->mInfo['blog_id'];
	include( THEMES_PKG_PATH.'tc_inc.php' );
}

if ($feature_blogposts_comments == 'y') {
	$maxComments = $gBitSystem->getPreference( 'blog_comments_per_page' );
	$comments_return_url = $PHP_SELF."?post_id=$post_id";
	$commentsParentId = $gContent->mContentId;
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}


$gBitSystem->setBrowserTitle( $gContent->mInfo['title'] );
// Display the template
$smarty->display("bitpackage:blogs/print_blog_post.tpl");

?>
