<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/edit.php,v 1.23 2007/03/26 16:32:09 wjames5 Exp $
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
require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

$gBitSystem->verifyPackage( 'blogs' );

if (!isset($last_modified)) {
	$last_modified=time();
	$gBitSmarty->assign('last_modified', $last_modified);
}

$gBitSmarty->assign_by_ref('heading', $heading);

/* DEPRECATED slated for removal -wjames5
if( $gBlog->isValid() ) {
	$gContent = &$gContent; // make a reference so services work correctly
	$_REQUEST['content_id'] = $gBlog->mContentId;
	if( !$gBlog->hasEditPermission() ) {
		$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot edit this blog" );
	}
}
*/

if( $gContent->isValid() ) {
	$_REQUEST['content_id'] = $gContent->mContentId;
	if( !$gContent->hasEditPermission() ) {
		$gBitSystem->verifyPermission( 'p_blogs_admin', "Permission denied you cannot edit this blog" );
	}
}


// Now check permissions to access this page
if (!$gBitUser->hasPermission( 'p_blogs_create' ) && ($gBitUser->mUserId != $data['user_id'] || !$gBitUser->mUserId) ) {
	$gBitSystem->fatalPermission('p_blog_create');
}

// WYSIWYG and Quicktag variable
$gBitSmarty->assign( 'textarea_id', LIBERTY_TEXT_AREA );

if (isset($_REQUEST['preview'])) {
	//all this should prolly be moved to a BitBlog::preparePreview method and the tpls cleaned - but this works for now -wjames5
	$gBitSmarty->assign('title', $_REQUEST["title"]);
	$gBitSmarty->assign('edit', $_REQUEST["edit"]);
	$gBitSmarty->assign('parsed', $gContent->parseData( $_REQUEST["edit"], $_REQUEST["format_guid"]));	
	$gBitSmarty->assign('user_name', $gBitUser->getDisplayName());	
	$gBitSmarty->assign('created', $gBitSystem->getUTCTime());

//DEPRECATED - slated for removal
//	$gBitSmarty->assign('public_blog', isset($_REQUEST["public_blog"]) ? 'y' : 'n');

	$gBitSmarty->assign('use_find', isset($_REQUEST["use_find"]) ? 'y' : 'n');
	$gBitSmarty->assign('use_title', isset($_REQUEST["use_title"]) ? 'y' : 'n');
	$gBitSmarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$gBitSmarty->assign('max_posts', $_REQUEST["max_posts"]);
	$gBitSmarty->assign('heading', $heading);
	$gContent->invokeServices('content_preview_function');	
} else {
	$gContent->invokeServices('content_edit_function');
}

if (isset($_REQUEST['save_blog'])) {
	if( $gContent->store( $_REQUEST ) ) {
		bit_redirect( $gContent->getDisplayUrl() );
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

$gBitSystem->setBrowserTitle( tra( 'Edit Blog' ).' - '.$gContent->getTitle() );
// Display the Index Template
$gBitSmarty->assign('show_page_bar', 'n');
// Let services work on blogs
if( $gBitUser->hasPermission( 'p_liberty_assign_content_perms' ) ) {
	require_once( LIBERTY_PKG_PATH.'content_permissions_inc.php' );
}

$gBitSmarty->assign_by_ref('gContent', $gContent);
$gBitSystem->display( 'bitpackage:blogs/edit_blog.tpl');

?>
