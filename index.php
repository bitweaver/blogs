<?php

// $Header: /cvsroot/bitweaver/_bit_blogs/index.php,v 1.1 2005/06/19 03:57:42 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

if (defined("CATEGORIES_PKG_PATH")) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}

$gBitSystem->verifyPackage( 'blogs' );

if ($gBitSystem->isPackageActive( 'categories' )) {
	if (isset($_REQUEST['addcateg']) and $_REQUEST['addcateg'] and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->categorize_blog_post($_REQUEST['post_id'],$_REQUEST['addcateg'],true);
	} elseif (isset($_REQUEST['delcategs']) and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->uncategorize('blogpost',$_REQUEST['post_id']);
	}

	$categs = $categlib->list_all_categories(0, -1, 'name_asc', '', '', 0);
	$smarty->assign('categs',$categs['data']);
	$smarty->assign('page','index.php');
	$choosecateg = str_replace('"',"'",$smarty->fetch('bitpackage:blogs/popup_categs.tpl'));
	$smarty->assign('choosecateg',$choosecateg);
}

$smarty->assign( 'showEmpty', TRUE );
$gDefaultCenter = 'bitpackage:blogs/center_list_blog_posts.tpl';
$smarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List Blog Posts' );

?>
