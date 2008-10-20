<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/list_blogs.php,v 1.20 2008/10/20 21:40:09 spiderr Exp $
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
require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$gBitSystem->verifyPackage( 'blogs' );

$gBitSystem->verifyPermission( 'p_blogs_view' );

$gBitSystem->setBrowserTitle(tra("View All Blogs"));

if( $gContent->isValid() && isset($_REQUEST["remove"])) {
	$gBitSystem->setBrowserTitle(tra("Delete Blog"));

	// Check if has edit perm of this blog
	$gContent->verifyUpdatePermission();
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = $_REQUEST["remove"];
		$formHash['blog_id'] = $gContent->mBlogId;
		$gBitSystem->confirmDialog( $formHash, 
			array(
				'warning' => tra('Are you sure you want to delete this blog?') . ' ' . $gContent->getTitle(), 
				'error' => tra('This cannot be undone!'),
			)
		);
	} else {
		$gContent->expunge();
	}
}

// Get a list of last changes to the Wiki database
$blogsList = $gContent->getList( $_REQUEST );
$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );
$gBitSmarty->assign_by_ref( 'blogsList', $blogsList );

// Display the template
$gBitSystem->display( 'bitpackage:blogs/list_blogs.tpl', NULL, array( 'display_mode' => 'list' ));

?>
