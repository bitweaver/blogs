<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/view.php,v 1.1.1.1.2.12 2006/06/10 09:15:43 squareing Exp $

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

if ($gBitSystem->isPackageActive( 'categories' )) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$gBitSystem->verifyPackage( 'blogs' );
$gBitSmarty->assign('showBlogTitle', 'y');

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

$gBitSmarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission( $_REQUEST["blog_id"], $gBlog->getContentType() )) {
	$gBitSmarty->assign('individual', 'y');

	if (!$gBitUser->isAdmin()) {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		//$perms = $gBitUser->getPermissions('', BLOGS_PKG_NAME );
		$perms = $gBitSystem->getPermissionInfo(NULL, BLOGS_PKG_NAME);

		foreach ($perms as $perm_name => $permInfo) {
			//$perm_name = $perm["perm_name"];

			if ($gBitUser->object_has_permission( $gBitUser->mUserId, $_REQUEST["blog_id"], $gBlog->getContentType(), $perm_name ) ) {
				$$perm_name = 'y';

				$gBitSmarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$gBitSmarty->assign("$perm_name", 'n');
			}
		}
	}
}

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

if ($gBitSystem->isPackageActive( 'categories' ) && function_exists( 'categories_display' ) ) {
	$gBlog->mContentId = $_REQUEST["blog_id"];
	categories_display( $gBlog );
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
$blog_data = $gBlog->get_blog($_REQUEST["blog_id"]);

if( !empty( $blog_data['blog_style'] ) && $gBitSystem->getPreference('feature_user_theme') == 'h' ) {
	$gBitSystem->setStyle( $blog_data['blog_style'] );
	$gBitSystem->mStyles['styleSheet'] = $gBitSystem->getStyleCss( $blog_data['blog_style'], $blog_data['user_id'] );
	$gBitSmarty->assign( 'userStyle', $blog_data['blog_style'] );
}

$ownsblog = ($gBitUser->mUserId == $blog_data["user_id"] ) ? 'y' : 'n';
$gBitSmarty->assign('ownsblog', $ownsblog);

if (!$blog_data) {
	$gBitSystem->fatalError( 'Blog not found' );
}

$gBlog->add_blog_hit($_REQUEST["blog_id"]);
$gBitSmarty->assign('blog_id', $_REQUEST["blog_id"]);
$gBitSmarty->assign('title', $blog_data["title"]);
$gBitSmarty->assign('heading', $blog_data["heading"]);
$gBitSmarty->assign('use_title', $blog_data["use_title"]);
$gBitSmarty->assign('use_find', $blog_data["use_find"]);
$gBitSmarty->assign('allow_comments', $blog_data["allow_comments"]);
$gBitSmarty->assign('description', $blog_data["description"]);
$gBitSmarty->assign('created', $blog_data["created"]);
$gBitSmarty->assign('last_modified', $blog_data["last_modified"]);
$gBitSmarty->assign('posts', $blog_data["posts"]);
$gBitSmarty->assign('public', $blog_data["public"]);
$gBitSmarty->assign('hits', $blog_data["hits"]);
$gBitSmarty->assign('creator', $blog_data["user_id"]);
$gBitSmarty->assign('activity', $blog_data["activity"]);
$gBitSmarty->assign('avatar', $blog_data["avatar"]);
$gBitSmarty->assign_by_ref('blog_data', $blog_data);

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


$now = $gBitSystem->getUTCTime();

$blogPost = new BitBlogPost();
$listHash['blog_id'] = $_REQUEST['blog_id'];
$listHash['parse_data'] = TRUE;
$listHash['max_records'] = $blog_data['max_posts'];
$listHash['load_num_comments'] = TRUE;
$listHash['page'] = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);
$listHash['offset'] = (!empty($_REQUEST['offset']) ? $_REQUEST['offset'] : 0);
$blogPosts = $blogPost->getList( $listHash );
//$blogPosts = $blogPost->getList($_REQUEST["blog_id"], $offset, $blog_data["max_posts"], $sort_mode, $find );
if (!empty($_REQUEST['offset'])) {
	$offset = $_REQUEST['offset'];
} else {
	$offset = 0;
}
$cant_pages = ceil($blogPosts["cant"] / $blog_data["max_posts"]);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($listHash['offset'] / $blog_data["max_posts"]));
$gBitSmarty->assign_by_ref('offset', $listHash['offset']);
$gBitSmarty->assign_by_ref('sort_mode', $listHash['sort_mode']);

if ($blogPosts["cant"] > ($listHash['offset'] + $blog_data["max_posts"])) {
	$gBitSmarty->assign('next_offset', $offset + $blog_data["max_posts"]);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($listHash['offset'] > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $blog_data["max_posts"]);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

// If there're more records then assign next_offset
$gBitSmarty->assign_by_ref('blogPosts', $blogPosts["data"]);
//print_r($blogPosts["data"]);

if( $gBitSystem->isFeatureActive( 'feature_theme_control' ) ) {
	$cat_obj_type = BITBLOG_CONTENT_TYPE_GUID;
	$cat_objid = $gBlog->mContentId;
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
				$gBitUser->storeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'], tra('blog'), $blogPost->mInfo['title'], $blogPost->getDisplayUrl() );
			}
		} else {
			$gBitUser->expungeWatch( $_REQUEST['watch_event'], $_REQUEST['watch_object'] );
		}
	}

	$gBitSmarty->assign('user_watching_blog', 'n');

	if ( $watch = $gBitUser->getEventWatches( $gBitUser->mUserId, 'blog_post', $_REQUEST['blog_id'])) {
		$gBitSmarty->assign('user_watching_blog', 'y');
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
