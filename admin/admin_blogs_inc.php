<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/admin/admin_blogs_inc.php,v 1.1.1.1.2.3 2005/07/26 15:50:02 drewslater Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	
	$gBitSystem->storePreference("home_blog", $_REQUEST["homeBlog"]);
	$gBitSmarty->assign('home_blog', $_REQUEST["homeBlog"]);
}

require_once( BLOGS_PKG_PATH.'BitBlog.php' );
if (defined("CATEGORIES_PKG_PATH")  and $gBitSystem->isPackageActive( 'categories' )) {
	include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
	$categs = $categlib->get_all_categories();
	$gBitSmarty->assign('categs',$categs);
}

$formBlogLists = array(
	"blog_list_title" => array(
		'label' => 'Title',
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
		'label' => 'Creator',
		'note' => 'The creator of a particular blog.',
	),
	"blog_list_posts" => array(
		'label' => 'Posts',
		'note' => 'Number of posts submitted to any given blog.',
	),
	"blog_list_visits" => array(
		'label' => 'Visits',
		'note' => 'Number of times a given blog has been visited.',
	),
	"blog_list_activity" => array(
		'label' => 'Activity',
		'note' => 'This number is an indication of how active a given blog is. The number is calculated based on god knows what...',
	),
);
$gBitSmarty->assign( 'formBlogLists',$formBlogLists );

$formBlogFeatures = array(
	"feature_blog_rankings" => array(
		'label' => 'Rankings',
		'note' => 'Enable the use of rankings based on page hits.',
	),
	"feature_blogposts_comments" => array(
		'label' => 'Blog Post Comments',
		'note' => 'Allow the addition of comments to blog posts.',
	),
	"blog_spellcheck" => array(
		'label' => 'Spellchecking',
	),
);
$gBitSmarty->assign( 'formBlogFeatures',$formBlogFeatures );

$processForm = set_tab();

if( $processForm ) {
	
	$blogToggles = array_merge( $formBlogLists,$formBlogFeatures );
	foreach( $blogToggles as $item => $data ) {
		simple_set_toggle( $item );
	}

	$gBitSystem->storePreference("feature_blogposts_comments", isset( $_REQUEST["feature_blogposts_comments"] ) ? 'y' : 'n');
	$gBitSystem->storePreference("blog_list_order", $_REQUEST["blog_list_order"]);
	$gBitSystem->storePreference("blog_list_user", $_REQUEST["blog_list_user"]);
	$gBitSmarty->assign('blog_list_order', $_REQUEST["blog_list_order"]);
	$gBitSmarty->assign('blog_list_user', $_REQUEST['blog_list_user']);
	
	if ($gBitSystem->isPackageActive( 'categories' )) {
		if (isset($_REQUEST["blog_categ"]) && $_REQUEST["blog_categ"] == "on") {
			$gBitSystem->storePreference("blog_categ", 'y');
			$gBitSmarty->assign("blog_categ", 'y');
		} else {
			$gBitSystem->storePreference("blog_categ", 'n');
			$gBitSmarty->assign("blog_categ", 'n');
		}
		$gBitSystem->storePreference("blog_parent_categ", $_REQUEST["blog_parent_categ"]);
		$gBitSmarty->assign('blog_parent_categ', $_REQUEST['blog_parent_categ']);
	}
}

$blogs = $gBlog->list_blogs(0, -1, 'created_desc', '');
$gBitSmarty->assign_by_ref('blogs', $blogs["data"]);
?>
