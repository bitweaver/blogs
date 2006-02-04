<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/list_blogs.php,v 1.5 2006/02/04 19:04:34 squareing Exp $
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

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

if (isset($_REQUEST["remove"])) {
	// Check if it is the owner
	if( $data = $gBlog->get_blog($_REQUEST["remove"]) ) {
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['remove'] = $_REQUEST["remove"];
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the blog '.$data['title'].'?', 'error' => 'This cannot be undone!' ) );
		} else {

			if ($data["user_id"] != $gBitUser->mUserId) {
				$gBitSystem->verifyPermission( 'bit_p_blog_admin', "Permission denied you cannot remove this blog" );
			}

			$gBlog->expunge($_REQUEST["remove"]);
		}
	}
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = $gBitSystem->getPreference( 'blog_list_order', 'created_desc' );
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use last_modified_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

if (isset($_REQUEST['page'])) {
	$page = &$_REQUEST['page'];
	$offset = ($page - 1) * $maxRecords;
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);

// Get a list of last changes to the Wiki database
$listpages = $gBlog->list_blogs($offset, $maxRecords, $sort_mode, $find);

for ($i = 0; $i < count($listpages["data"]); $i++) {
	if ($gBitUser->object_has_one_permission($listpages["data"][$i]["blog_id"], 'blog')) {
		$listpages["data"][$i]["individual"] = 'y';

		if ($gBitUser->object_has_permission($user, $listpages["data"][$i]["blog_id"], 'blog', 'bit_p_read_blog')) {
			$listpages["data"][$i]["individual_bit_p_read_blog"] = 'y';
		} else {
			$listpages["data"][$i]["individual_bit_p_read_blog"] = 'n';
		}

		if ($gBitUser->object_has_permission($user, $listpages["data"][$i]["blog_id"], 'blog', 'bit_p_blog_post')) {
			$listpages["data"][$i]["individual_bit_p_blog_post"] = 'y';
		} else {
			$listpages["data"][$i]["individual_bit_p_blog_post"] = 'n';
		}

		if ($gBitUser->object_has_permission($user, $listpages["data"][$i]["blog_id"], 'blog', 'bit_p_create_blogs')) {
			$listpages["data"][$i]["individual_bit_p_create_blogs"] = 'y';
		} else {
			$listpages["data"][$i]["individual_bit_p_create_blogs"] = 'n';
		}

		if ($gBitUser->isAdmin() || $gBitUser->object_has_permission($user, $listpages["data"][$i]["blog_id"], 'blog', 'bit_p_blog_admin'))
			{
			$listpages["data"][$i]["individual_bit_p_create_blogs"] = 'y';

			$listpages["data"][$i]["individual_bit_p_blog_post"] = 'y';
			$listpages["data"][$i]["individual_bit_p_read_blog"] = 'y';
		}
	} else {
		$listpages["data"][$i]["individual"] = 'n';
	}
}

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listpages["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('listpages', $listpages["data"]);

$gBitSystem->setBrowserTitle("View All Blogs");
// Display the template
$gBitSystem->display( 'bitpackage:blogs/list_blogs.tpl');

?>
