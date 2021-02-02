<?php
/**
 * @version $Header$
 *
 * @package blogs
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

include_once( BLOGS_PKG_CLASS_PATH.'BitBlog.php' );

if (!isset($_REQUEST["image_id"])) {
	$gBitSmarty->assign('msg', tra("No image id given"));
	$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'display' ));
	die;
}

$imageInfo = $gBlog->getStorageFileInfo($_REQUEST["image_id"]);
$gBitSmarty->assign( 'imageInfo' , $imageInfo );
$gBitSystem->display( 'bitpackage:blogs/view_post_image.tpl' , NULL, array( 'display_mode' => 'display' ));
?>
