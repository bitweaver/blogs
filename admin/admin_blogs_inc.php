<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/admin/admin_blogs_inc.php,v 1.1 2005/06/19 03:57:42 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	
	$gBitSystem->storePreference("home_blog", $_REQUEST["homeBlog"]);
	$smarty->assign('home_blog', $_REQUEST["homeBlog"]);
}

require_once( BLOGS_PKG_PATH.'BitBlog.php' );
if (defined("CATEGORIES_PKG_PATH")  and $gBitSystem->isPackageActive( 'categories' )) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
	$categs = $categlib->get_all_categories();
	$smarty->assign('categs',$categs);
}

$formBlogLists = array(
	"blog_list_title" => array(
		'label' => 'Title',
		'note' => '',
		'page' => '',
	),
	"blog_list_description" => array(
		'label' => 'Description',
	),
	"blog_list_created" => array(
		'label' => 'Creation date',
	),
	"blog_list_lastmodif" => array(
		'label' => 'Last modification time',
	),
	"blog_list_user" => array(
		'label' => 'User',
	),
	"blog_list_posts" => array(
		'label' => 'Posts',
	),
	"blog_list_visits" => array(
		'label' => 'Visits',
	),
	"blog_list_activity" => array(
		'label' => 'Activity',
	),
);
$smarty->assign( 'formBlogLists',$formBlogLists );

$formBlogFeatures = array(
	"feature_blog_rankings" => array(
		'label' => 'Rankings',
	),
	"blog_spellcheck" => array(
		'label' => 'Spellchecking',
	),
);
$smarty->assign( 'formBlogFeatures',$formBlogFeatures );

$processForm = set_tab();

if( $processForm ) {
	
	$blogToggles = array_merge( $formBlogLists,$formBlogFeatures );
	foreach( $blogToggles as $item => $data ) {
		simple_set_toggle( $item );
	}

	$gBitSystem->storePreference("feature_blogposts_comments", isset( $_REQUEST["feature_blogposts_comments"] ) ? 'y' : 'n');
	$gBitSystem->storePreference("blog_list_order", $_REQUEST["blog_list_order"]);
	$gBitSystem->storePreference("blog_list_user", $_REQUEST["blog_list_user"]);
	$smarty->assign('blog_list_order', $_REQUEST["blog_list_order"]);
	$smarty->assign('blog_list_user', $_REQUEST['blog_list_user']);
	
	if ($gBitSystem->isPackageActive( 'categories' )) {
		if (isset($_REQUEST["blog_categ"]) && $_REQUEST["blog_categ"] == "on") {
			$gBitSystem->storePreference("blog_categ", 'y');
			$smarty->assign("blog_categ", 'y');
		} else {
			$gBitSystem->storePreference("blog_categ", 'n');
			$smarty->assign("blog_categ", 'n');
		}
		$gBitSystem->storePreference("blog_parent_categ", $_REQUEST["blog_parent_categ"]);
		$smarty->assign('blog_parent_categ', $_REQUEST['blog_parent_categ']);
	}

	if (isset($_REQUEST["blog_comments_per_page"])) {
		$gBitSystem->storePreference("blog_comments_per_page", $_REQUEST["blog_comments_per_page"]);

		$smarty->assign('blog_comments_per_page', $_REQUEST["blog_comments_per_page"]);
	}

	if (isset($_REQUEST["blog_comments_default_ordering"])) {
		$gBitSystem->storePreference("blog_comments_default_ordering", $_REQUEST["blog_comments_default_ordering"]);

		$smarty->assign('blog_comments_default_ordering', $_REQUEST["blog_comments_default_ordering"]);
	}
}


$blogs = $gBlog->list_blogs(0, -1, 'created_desc', '');
$smarty->assign_by_ref('blogs', $blogs["data"]);
?>
