<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/list_blogs.php,v 1.10 2006/11/18 16:16:37 spiderr Exp $
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

$gBitSystem->verifyPermission( 'p_blogs_view' );

if( $gBlog->isValid() && isset($_REQUEST["remove"])) {
	// Check if has edit perm of this blog
	if( $gBlog->hasUserPermission( 'p_blog_edit', TRUE ) ) {
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['remove'] = $_REQUEST["remove"];
			$formHash['blog_id'] = $gBlog->mBlogId;
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the blog '.$gBlog->getTitle().'? All posts will be permanently deleted.', 'error' => 'This cannot be undone!' ) );
		} else {
			$gBlog->expunge();
		}
	}
}

// Get a list of last changes to the Wiki database
$listBlogs = $gBlog->getList( $_REQUEST );

$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );
$gBitSmarty->assign( 'listpages', $listBlogs["data"] );

for ($i = 0; $i < count($listBlogs["data"]); $i++) {
	if ($gBitUser->object_has_one_permission($listBlogs["data"][$i]["blog_id"], 'blog')) {
		$listBlogs["data"][$i]["individual"] = 'y';

		if ($gBitUser->object_has_permission($user, $listBlogs["data"][$i]["blog_id"], 'blog', 'p_blogs_view')) {
			$listBlogs["data"][$i]["individual_bit_p_read_blog"] = 'y';
		} else {
			$listBlogs["data"][$i]["individual_bit_p_read_blog"] = 'n';
		}

		if ($gBitUser->object_has_permission($user, $listBlogs["data"][$i]["blog_id"], 'blog', 'p_blogs_post')) {
			$listBlogs["data"][$i]["individual_bit_p_blog_post"] = 'y';
		} else {
			$listBlogs["data"][$i]["individual_bit_p_blog_post"] = 'n';
		}

		if ($gBitUser->object_has_permission($user, $listBlogs["data"][$i]["blog_id"], 'blog', 'p_blogs_create')) {
			$listBlogs["data"][$i]["individual_bit_p_create_blogs"] = 'y';
		} else {
			$listBlogs["data"][$i]["individual_bit_p_create_blogs"] = 'n';
		}

		if ($gBitUser->isAdmin() || $gBitUser->object_has_permission($user, $listBlogs["data"][$i]["blog_id"], 'blog', 'p_blogs_admin'))
			{
			$listBlogs["data"][$i]["individual_bit_p_create_blogs"] = 'y';

			$listBlogs["data"][$i]["individual_bit_p_blog_post"] = 'y';
			$listBlogs["data"][$i]["individual_bit_p_read_blog"] = 'y';
		}
	} else {
		$listBlogs["data"][$i]["individual"] = 'n';
	}
}

$gBitSmarty->assign_by_ref('listpages', $listBlogs["data"]);

$gBitSystem->setBrowserTitle("View All Blogs");
// Display the template
$gBitSystem->display( 'bitpackage:blogs/list_blogs.tpl');

?>
