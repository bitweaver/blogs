<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/edit.php,v 1.13 2006/11/18 15:41:21 spiderr Exp $
 * @package blogs
 * @subpackage functions
 */
//

// @package blogs

// Copyright (c) 2004-2006, bitweaver.org
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

if (!isset($last_modified)) {
	$last_modified=time();
	$gBitSmarty->assign('last_modified', $last_modified);
}

$gBitSmarty->assign_by_ref('heading', $heading);

if( $gBlog->isValid() ) {
	if( !$gBlog->hasEditPermission() ) {
		$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot edit this blog" );
	}
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

	if( $gBlog->store( $_REQUEST ) ) {
		bit_redirect( $gBlog->getDisplayUrl() );
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gBlog->mErrors );
	}
}

$gBitSystem->setBrowserTitle("Edit Blog Post - ".$data['title']);
// Display the Index Template
$gBitSmarty->assign('show_page_bar', 'n');
$gBitSystem->display( 'bitpackage:blogs/edit_blog.tpl');

?>
