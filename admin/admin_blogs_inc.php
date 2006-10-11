<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/admin/admin_blogs_inc.php,v 1.9 2006/10/11 06:05:13 spiderr Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	$gBitSystem->storeConfig("home_blog", $_REQUEST["homeBlog"], BLOGS_PKG_NAME);
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
	"blog_rankings" => array(
		'label' => 'Rankings',
		'note' => 'Enable the use of rankings based on page hits.',
	),
	"blog_posts_comments" => array(
		'label' => 'Blog Post Comments',
		'note' => 'Allow the addition of comments to blog posts.',
	),
);
$gBitSmarty->assign( 'formBlogFeatures',$formBlogFeatures );

$processForm = set_tab();

if( $processForm ) {
	$blogToggles = array_merge( $formBlogLists,$formBlogFeatures );
	foreach( $blogToggles as $item => $data ) {
		simple_set_toggle( $item, BLOGS_PKG_NAME );
	}

	$gBitSystem->storeConfig("blog_posts_comments", isset( $_REQUEST["blog_posts_comments"] ) ? 'y' : 'n', BLOGS_PKG_NAME );
	$gBitSystem->storeConfig("blog_list_order", $_REQUEST["blog_list_order"], BLOGS_PKG_NAME );
	$gBitSystem->storeConfig("blog_list_user", $_REQUEST["blog_list_user"], BLOGS_PKG_NAME );
	$gBitSmarty->assign('blog_list_order', $_REQUEST["blog_list_order"]);
	$gBitSmarty->assign('blog_list_user', $_REQUEST['blog_list_user']);

	if ($gBitSystem->isPackageActive( 'categories' )) {
		if (isset($_REQUEST["blog_categ"]) && $_REQUEST["blog_categ"] == "on") {
			$gBitSystem->storeConfig("blog_categ", 'y', BLOGS_PKG_NAME );
			$gBitSmarty->assign("blog_categ", 'y');
		} else {
			$gBitSystem->storeConfig("blog_categ", 'n', BLOGS_PKG_NAME );
			$gBitSmarty->assign("blog_categ", 'n');
		}
		$gBitSystem->storeConfig("blog_parent_categ", $_REQUEST["blog_parent_categ"], BLOGS_PKG_NAME );
		$gBitSmarty->assign('blog_parent_categ', $_REQUEST['blog_parent_categ']);
	}
}

$listHash['sort_mode'] = 'created_desc';
$blogs = $gBlog->getList( $listHash );
$gBitSmarty->assign_by_ref('blogs', $blogs["data"]);
?>
