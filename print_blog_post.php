<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/print_blog_post.php,v 1.1.1.1.2.5 2005/08/12 11:38:53 wolff_borg Exp $

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

if (!isset($_REQUEST["post_id"])) {
	$gBitSystem->fatalError( 'No post indicated' );
}

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

$gBitSmarty->assign('post_info', $gContent->mInfo );

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mInfo['post_id'];
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mInfo['post_id'];
$gBitSmarty->assign('uri', $uri);
$gBitSmarty->assign('uri2', $uri2);

if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

$gBitSmarty->assign('offset', $_REQUEST["offset"]);
$gBitSmarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$gBitSmarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];

$gBitSmarty->assign( 'parsed_data', $gContent->parseData() );

$gBitSmarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($gContent->mInfo['blog_id'], 'blog')) {
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

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

$ownsblog = 'n';

if ($gBitUser->mUserId && $gBitUser->mUserId == $gContent->mInfo['blog_user_id'] ) {
	$ownsblog = 'y';
}

$gBitSmarty->assign('ownsblog', $ownsblog);

if ($feature_theme_control == 'y') {
	$cat_obj_type = BITBLOG_CONTENT_TYPE_GUID;
	$cat_objid = $gContent->mContentId;
	include( THEMES_PKG_PATH.'tc_inc.php' );
}

if ($feature_blogposts_comments == 'y') {
	$comments_return_url = $PHP_SELF."?post_id=$post_id";
	$commentsParentId = $gContent->mContentId;
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}


$gBitSystem->setBrowserTitle( $gContent->mInfo['title'] );
// Display the template
$gBitSmarty->display("bitpackage:blogs/print_blog_post.tpl");

?>
