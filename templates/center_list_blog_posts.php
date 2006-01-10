<?php

global $gBitSmarty, $gBlog, $gBitSystem, $categlib, $_REQUEST, $maxRecords, $gQueryUserId, $package_categories;
$postRecords = ( $module_rows ? $module_rows : $maxRecords );

if (defined("CATEGORIES_PKG_PATH")) {
	include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

if ($gBitSystem->isPackageActive( 'vvcat' )) {
	if (isset($_REQUEST['addcateg']) and $_REQUEST['addcateg'] and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->categorize_blog_post($_REQUEST['post_id'],$_REQUEST['addcateg'],true);
	} elseif (isset($_REQUEST['delcategs']) and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->uncategorize('blogpost',$_REQUEST['post_id']);
	}
	$categs = $categlib->list_all_categories(0, -1, 'name_asc', '', '', 0);

	$gBitSmarty->assign('categs',$categs['data']);
	$gBitSmarty->assign('page','view.php');
	$choosecateg = str_replace('"',"'",$gBitSmarty->fetch('bitpackage:blogs/popup_categs.tpl'));
	$gBitSmarty->assign('choosecateg',$choosecateg);
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'created_desc';
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
	$page = $_REQUEST['page'];
	$offset = ($page - 1) * $postRecords;
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$gBitSmarty->assign('find', $find);

$gBitSmarty->assign('showBlogTitle', 'y');

$listHash['max_records'] = $postRecords;
$listHash['parse_data'] = TRUE;
$listHash['load_comments'] = TRUE;
$listHash['page'] = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);
$listHash['offset'] = (!empty($_REQUEST['offset']) ? $_REQUEST['offset'] : 0);

if( @BitBase::verifyId( $module_params['blog_id'] ) ) {
	$listHash['blog_id'] = $module_params['blog_id'];
}
if( @BitBase::verifyId( $module_params['user_id'] ) ) {
	$listHash['user_id'] = $module_params['user_id'];
}
if ( !empty( $module_params['sort_mode'] ) ) {
	$listHash['sort_mode'] = $module_params['sort_mode'];
}

// Get a list of last changes to the Wiki database
if ($gQueryUserId) {
	$listHash['user_id'] = $gQueryUserId;
} elseif( $_REQUEST['user_id'] ) {
	$listHash['user_id'] = $_REQUEST['user_id'];
}

$blogPost = new BitBlogPost();
if( empty( $gContent ) ) {
	$gBitSmarty->assign_by_ref( 'gContent', $blogPost );
}
$blogPosts = $blogPost->getList( $listHash );

// If there're more records then assign next_offset
$cant_pages = ceil($blogPosts["cant"] / $postRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $postRecords));

if ($blogPosts["cant"] > ($offset + $postRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $postRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $postRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}
$gBitSmarty->assign_by_ref('gQueryUserId', $listHash['user_id']);
$gBitSmarty->assign_by_ref('blogPosts', $blogPosts["data"]);
//print_r($blogPosts["data"]);


?>
