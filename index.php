<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/index.php,v 1.7 2007/03/18 18:49:58 wjames5 Exp $

 * @package blogss
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

//"DEPRECATED - Slated for removal
/*
if (defined("CATEGORIES_PKG_PATH")) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}
*/

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyPermission( 'p_blogs_view' );

//"DEPRECATED - Slated for removal
/*
if ($gBitSystem->isPackageActive( 'categories' )) {
	if (isset($_REQUEST['addcateg']) and $_REQUEST['addcateg'] and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->categorize_blog_post($_REQUEST['post_id'],$_REQUEST['addcateg'],true);
	} elseif (isset($_REQUEST['delcategs']) and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->uncategorize('blogpost',$_REQUEST['post_id']);
	}

	$categs = $categlib->list_all_categories(0, -1, 'name_asc', '', '', 0);
	$gBitSmarty->assign('categs',$categs['data']);
	$gBitSmarty->assign('page','index.php');
	$choosecateg = str_replace('"',"'",$gBitSmarty->fetch('bitpackage:blogs/popup_categs.tpl'));
	$gBitSmarty->assign('choosecateg',$choosecateg);
}
*/

$gBitSmarty->assign( 'showEmpty', TRUE );
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );

?>
