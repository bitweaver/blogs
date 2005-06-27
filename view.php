<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view.php,v 1.1.1.1.2.2 2005/06/27 10:08:40 lsces Exp $

 * @package blogs
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$iPageTitle = 'test';
/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

if (defined("CATEGORIES_PKG_PATH")) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
	
$gBitSystem->verifyPackage( 'blogs' );
$smarty->assign('showBlogTitle', 'y');

if (isset($_REQUEST['user_id']) && !isset($_REQUEST['blog_id'])) {
	// We will try and grab the first blog owned by the user id given
	$blogsList = $gBlog->list_user_blogs($_REQUEST['user_id']);
	if (!empty($blogsList[0]['blog_id'])) {
		$_REQUEST['blog_id'] = $blogsList[0]['blog_id'];		
	}
}

if (!isset($_REQUEST["blog_id"])) {
	$gBitSystem->fatalError( 'No blog indicated' );
}

$smarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission( $_REQUEST["blog_id"], $gBlog->getContentType() )) {
	$smarty->assign('individual', 'y');

	if (!$gBitUser->isAdmin()) {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		//$perms = $gBitUser->getPermissions('', BLOGS_PKG_NAME );
		$perms = $gBitSystem->getPermissionInfo(NULL, BLOGS_PKG_NAME);

		foreach ($perms as $perm_name => $permInfo) {
			//$perm_name = $perm["perm_name"];

			if ($gBitUser->object_has_permission( $gBitUser->mUserId, $_REQUEST["blog_id"], $gBlog->getContentType(), $perm_name ) ) {
				$$perm_name = 'y';

				$smarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$smarty->assign("$perm_name", 'n');
			}
		}
	}
}

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

if ($gBitSystem->isPackageActive( 'categories' )) {
	if (isset($_REQUEST['addcateg']) and $_REQUEST['addcateg'] and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->categorize_blog_post($_REQUEST['post_id'],$_REQUEST['addcateg'],true);
	} elseif (isset($_REQUEST['delcategs']) and isset($_REQUEST['post_id']) and $_REQUEST['post_id']) {
		$categlib->uncategorize('blogpost',$_REQUEST['post_id']);
	}
	$categs = $categlib->list_all_categories(0, -1, 'name_asc', '', '', 0);
	$smarty->assign('categs',$categs['data']);
	$smarty->assign('page','view.php');
	$choosecateg = str_replace('"',"'",$smarty->fetch('bitpackage:blogs/popup_categs.tpl'));
	$smarty->assign('choosecateg',$choosecateg);
}
$blog_data = $gBlog->get_blog($_REQUEST["blog_id"]);

if( !empty( $blog_data['blog_style'] ) && $gBitSystem->getPreference('feature_user_theme') == 'h' ) {
	$gBitSystem->setStyle( $blog_data['blog_style'] );
	$gBitLoc['styleSheet'] = $gBitSystem->getStyleCss( $blog_data['blog_style'], $blog_data['user_id'] );
	$smarty->assign( 'userStyle', $blog_data['blog_style'] );
}

$ownsblog = ($gBitUser->mUserId == $blog_data["user_id"] ) ? 'y' : 'n';
$smarty->assign('ownsblog', $ownsblog);

if (!$blog_data) {
	$gBitSystem->fatalError( 'Blog not found' );
}

$gBlog->add_blog_hit($_REQUEST["blog_id"]);
$smarty->assign('blog_id', $_REQUEST["blog_id"]);
$smarty->assign('title', $blog_data["title"]);
$smarty->assign('heading', $blog_data["heading"]);
$smarty->assign('use_title', $blog_data["use_title"]);
$smarty->assign('use_find', $blog_data["use_find"]);
$smarty->assign('allow_comments', $blog_data["allow_comments"]);
$smarty->assign('description', $blog_data["description"]);
$smarty->assign('created', $blog_data["created"]);
$smarty->assign('last_modified', $blog_data["last_modified"]);
$smarty->assign('posts', $blog_data["posts"]);
$smarty->assign('public', $blog_data["public"]);
$smarty->assign('hits', $blog_data["hits"]);
$smarty->assign('creator', $blog_data["user_id"]);
$smarty->assign('activity', $blog_data["activity"]);
$smarty->assign('avatar', $blog_data["avatar"]);
$smarty->assign_by_ref('blog_data', $blog_data);

if (isset($_REQUEST["remove"])) {
	
	$blogPost = new BitBlogPost( $_REQUEST["remove"] );
	if( $blogPost->load() ) {
		if( !$ownsblog && !$gBitUser->mUserId || $blogPost->mInfo["user_id"] != $gBitUser->mUserId) {
			$gBitSystem->verifyPermission( 'bit_p_blog_admin', "Permission denied you cannot remove this post" );
		}
		
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['remove'] = $_REQUEST['remove'];
			$formHash['blog_id'] = $_REQUEST['blog_id'];
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to remove post '.$_REQUEST['remove'].'?' ) );
		} else {
			$blogPost->expunge();
		}
	}
}


$now = date("U");

$blogPost = new BitBlogPost();
$listHash['blog_id'] = $_REQUEST['blog_id'];
$listHash['parse_data'] = TRUE;
$listHash['max_records'] = $blog_data['max_posts'];
$listHash['load_num_comments'] = TRUE;
$blogPosts = $blogPost->getList( $listHash );
//$blogPosts = $blogPost->getList($_REQUEST["blog_id"], $offset, $blog_data["max_posts"], $sort_mode, $find );
if (!empty($_REQUEST['offset'])) {
	$offset = $_REQUEST['offset'];
} else {
	$offset = 0;
}
$cant_pages = ceil($blogPosts["cant"] / $blog_data["max_posts"]);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($listHash['offset'] / $blog_data["max_posts"]));
$smarty->assign_by_ref('offset', $listHash['offset']);
$smarty->assign_by_ref('sort_mode', $listHash['sort_mode']);

if ($blogPosts["cant"] > ($listHash['offset'] + $blog_data["max_posts"])) {
	$smarty->assign('next_offset', $offset + $blog_data["max_posts"]);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($listHash['offset'] > 0) {
	$smarty->assign('prev_offset', $offset - $blog_data["max_posts"]);
} else {
	$smarty->assign('prev_offset', -1);
}

// If there're more records then assign next_offset
$smarty->assign_by_ref('blogPosts', $blogPosts["data"]);
//print_r($blogPosts["data"]);

if( $gBitSystem->isFeatureActive( 'feature_theme_control' ) ) {
	$cat_type = 'blog';
	$cat_objid = $_REQUEST['blog_id'];
	include( THEMES_PKG_PATH.'tc_inc.php' );
}

if( $gBitSystem->isPackageActive( 'notepad' ) && $gBitUser->hasPermission( 'bit_p_notepad' ) && isset($_REQUEST['savenotepad']) ) {
	
	$blogPost = new BitBlogPost( $_REQUEST['savenotepad'] );
	if( $blogPost->load() ) {
		$gBitSystem->replace_note( $gBitUser->mUserId, 0, $blogPost->mInfo['title'] ? $blogPost->mInfo['title'] : date("d/m/Y [h:i]", $blogPost->mInfo['created']), $blogPost->mInfo['data']);
	}
}

if( $gBitSystem->isFeatureActive( 'feature_user_watches' ) ) {
	if( $gBitUser->isValid() && isset( $_REQUEST['watch_event'] ) ) {
		
		if ($_REQUEST['watch_action'] == 'add') {
			$blogPost = new BitBlogPost( $_REQUEST['watch_object'] );
			if( $blogPost->load() ) {
				$gBitUser->storeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'], tra('blog'), $blogPost->mInfo['title'], $blogPost->getDisplayLink() );
			}
		} else {
			$gBitUser->expungeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'] );
		}
	}

	$smarty->assign('user_watching_blog', 'n');

	if ( $watch = $gBitUser->getEventWatches( $gBitUser->mUserId, 'blog_post', $_REQUEST['blog_id'])) {
		$smarty->assign('user_watching_blog', 'y');
	}
}

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once( HAWHAW_PKG_PATH.'hawtiki_lib.php' );

	HAWBIT_view_blog($blogPosts, $blog_data);
}



$gBitSystem->setBrowserTitle($blog_data['title']);
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog.tpl');
?>
