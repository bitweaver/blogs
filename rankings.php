<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/rankings.php,v 1.12 2008/06/25 22:21:07 spiderr Exp $

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

include_once( LIBERTY_PKG_PATH . 'LibertyContent.php' );
require_once( BLOGS_PKG_PATH . 'BitBlog.php' );
require_once( BLOGS_PKG_PATH . 'BitBlogPost.php' );

$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyFeature( 'blog_rankings' );
$gBitSystem->verifyPermission( 'p_blogs_view' );

$rankingOptions = array(
	array(
		'output' => tra( 'Most Often Viewed' ),
		'value' => 'hits_desc'
	),
	array(
		'output' => tra( 'Most Recently Modified' ),
		'value' => 'last_modified_desc'
	),
	array(
		'output' => tra( 'Most Active Authors' ),
		'value' => 'top_authors'
	),
);
$gBitSmarty->assign( 'rankingOptions', $rankingOptions );

if( !empty( $_REQUEST['sort_mode'] ) ) {
	switch( $_REQUEST['sort_mode'] ) {
		case 'last_modified_desc':
			$gBitSmarty->assign( 'attribute', 'last_modified' );
			$_REQUEST['attribute'] = tra( 'Date of last modification' );
			break;
		case 'top_authors':
			$gBitSmarty->assign( 'attribute', 'ag_hits' );
			$_REQUEST['attribute'] = tra( 'Hits to items by this Author' );
			break;
		default:
			$gBitSmarty->assign( 'attribute', 'hits' );
			$_REQUEST['attribute'] = tra( 'Hits' );
			break;
	}
} else {
	$gBitSmarty->assign( 'attribute', 'hits' );
	$_REQUEST['attribute'] = tra( 'Hits' );
}

$_REQUEST['title']             = tra( 'Blog Post Rankings' );
$_REQUEST['content_type_guid'] = BITBLOGPOST_CONTENT_TYPE_GUID;
$_REQUEST['max_records']       = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : 10;

if( empty( $gContent ) ) {
	$gContent = new LibertyContent();
}
$rankList = $gContent->getContentRanking( $_REQUEST );
$gBitSmarty->assign( 'rankList', $rankList );

$gBitSystem->display( 'bitpackage:liberty/rankings.tpl', tra( "Blog Post Rankings" ) , array( 'display_mode' => 'display' ));




/* ---- below is what blog rankings was - might want to canabalize some of it eventually ---- */
/* 
 
require_once( '../bit_setup_inc.php' );


$gBitSystem->verifyPackage( 'blogs' );

$gBitSystem->verifyFeature( 'blog_rankings' );

$gBitSystem->verifyPermission( 'p_blogs_view' );

require_once( BLOGS_PKG_PATH . 'BitBlog.php' );
require_once( BLOGS_PKG_PATH . 'BitBlogPost.php' );

$allrankings = array(
	array(
	'name' => tra('Most visited blogs'),
	'value' => 'blog_ranking_top_blogs'
),
	array(
	'name' => tra('Last posts'),
	'value' => 'blog_ranking_last_posts'
),
	/** 
	 * @todo reenable once we have activity implemented
	array(
	'name' => tra('Most active blogs'),
	'value' => 'blog_ranking_top_active_blogs'
)
	**/
/*
);

$gBitSmarty->assign('allrankings', $allrankings);

if (!isset($_REQUEST["which"])) {
	$which = 'blog_ranking_top_blogs';
} else {
	$which = $_REQUEST["which"];
}

$gBitSmarty->assign('which', $which);

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["limit"])) {
	$limit = 10;
} else {
	$limit = $_REQUEST["limit"];
}

$gBitSmarty->assign_by_ref('limit', $limit);

// Rankings:
// Top Pages
// Last pages
// Top Authors -- Would be nice.
$rankings = array();

$rankings = $which($limit);

$gBitSmarty->assign_by_ref('rankings', $rankings);
$gBitSmarty->assign('rpage', 'rankings.php');

// Display the template
$gBitSystem->display( 'bitpackage:blogs/ranking.tpl', tra($rankings['title']), array( 'display_mode' => 'display' ));

// =============================== some ranking functions - as soon as blogs are part of LibertyContent, we can use LibertyContent::getContentRanking()
function blog_ranking_top_blogs($limit) {
	global $gBitSystem;
	$list_hash['sort_mode'] = 'lch.hits_desc';
	$list_hash['max_records'] = $limit;
	$b = new BitBlog();
	$list = $b->getList($list_hash);
	$query = "select p.*, lc.* FROM `".BIT_DB_PREFIX."blog_posts` p LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (p.`content_id` = lc.`content_id`) WHERE p.blog_id = ? ORDER BY p.post_id desc";
	foreach($list['data'] as $key => $blog) {
		$result = $gBitSystem->mDb->query($query, array($blog['blog_id']), $gBitSystem->getConfig('blogs_top_post_count', 3));
		
		while ($ret = $result->fetchRow()) {
			$ret['display_url'] = BitBlogPost::getDisplayUrl($ret['content_id']);
			$list['data'][$key]['post_array'][] = $ret;
		}
	}
	$list['title'] = tra("Most Visited Blogs");
	return $list;
}

/** TODO: This should be changed when we start using activity in the blog.
          We should check TW 1.9 for code for that field in the blog. */
/*
function blog_ranking_top_active_blogs($limit) {
	global $gBitSystem;
	$list_hash['sort_mode'] = 'b.activity';
	$list_hash['max_records'] = $limit;
	$b = new BitBlog();
	$list = $b->getList($list_hash);
	$query = "select p.*, lc.* FROM `".BIT_DB_PREFIX."blog_posts` p LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (p.`content_id` = lc.`content_id`) WHERE p.blog_id = ? ORDER BY p.post_id desc";
	foreach($list['data'] as $key => $blog) {
		$result = $gBitSystem->mDb->query($query, array($blog['blog_id']), $gBitSystem->getConfig('blogs_top_post_count', 3));
		
		while ($ret = $result->fetchRow()) {
			$ret['display_url'] = BitBlogPost::getDisplayUrl($ret['content_id']);
			$list['data'][$key]['post_array'][] = $ret;
		}
	}
	$list['title'] = tra("Most Visited Blogs");
	return $list;
}

function blog_ranking_last_posts($limit) {
	global $gBitSystem;
	$list_hash['max_records'] = $limit;
	$list_hash['sort_mode'] = 'created_desc';
	$list_hash['max_records'] = $limit;
	$bp = new BitBlogPost();
	$posts = $bp->getList($list_hash);
	// Extract blog_ids to load the blogs.
	foreach( $posts['data'] as $key => $post) {
		$blog_ids[$post['blog_id']] = $post['blog_id'];
	}
	if (!empty($blog_ids)) {
	  $b = new BitBlog();
	  $blog_hash['sort_mode'] = 'lch.hits_desc';
	  $blog_hash['find'] = $blog_ids;
	  $blogs = $b->getList($blog_hash);
	  vd($blogs);
	  $list['data'] = array();
	  // Reorganize blogs by id
	  foreach($blogs['data'] as $key => $blog) {
	    $list['data'][$blog['blog_id']] = $blog;
	  }
	  // And merge in posts
	  foreach($posts['data'] as $key => $post) {
	    $post['post_url'] = $bp->getDisplayUrl($post['content_id']);
	    $list['data'][$post['blog_id']]['post_array'][] = $post;
	  }
	}
	$list['title'] = 'Last Posts';
	return $list;
}
*/
?>
