<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/edit.php,v 1.30 2007/09/22 22:24:22 spiderr Exp $
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

$gBitSystem->verifyPackage( 'blogs' );

require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyEditPermission();
} else {
	$gBitSystem->verifyPermission( 'p_blogs_create' );
}

if (isset($_REQUEST['preview'])) {
	//all this should prolly be moved to a BitBlog::preparePreview method and the tpls cleaned - but this works for now -wjames5
	$gBitSmarty->assign('title', $_REQUEST["title"]);
	$gBitSmarty->assign('edit', $_REQUEST["edit"]);
	$gBitSmarty->assign('parsed', $gContent->parseData( $_REQUEST["edit"], $_REQUEST["format_guid"]));	
	$gBitSmarty->assign('user_name', $gBitUser->getDisplayName());	
	$gBitSmarty->assign('created', $gBitSystem->getUTCTime());
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

$gBitSmarty->assign_by_ref('gContent', $gContent);
$gBitSystem->display( 'bitpackage:blogs/edit_blog.tpl');

?>
