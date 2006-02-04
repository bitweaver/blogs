<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/rankings.php,v 1.6 2006/02/04 10:10:50 squareing Exp $

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


$gBitSystem->verifyPackage( 'blogs' );

$gBitSystem->verifyFeature( 'feature_blog_rankings' );

$gBitSystem->verifyPermission( 'bit_p_read_blog' );

$allrankings = array(
	array(
	'name' => tra('Top visited blogs'),
	'value' => 'blog_ranking_top_blogs'
),
	array(
	'name' => tra('Last posts'),
	'value' => 'blog_ranking_last_posts'
),
	array(
	'name' => tra('Top active blogs'),
	'value' => 'blog_ranking_top_active_blogs'
)
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
// Top Authors
$rankings = array();

$rk = $which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"] = $rk["y"];
$rankings[] = $rank;

$gBitSmarty->assign_by_ref('rankings', $rankings);
$gBitSmarty->assign('rpage', 'rankings.php');


// Display the template
$gBitSystem->display( 'bitpackage:blogs/ranking.tpl');

// =============================== some ranking functions - as soon as blogs are part of LibertyContent, we can use LibertyContent::getContentRanking()
function blog_ranking_top_blogs($limit) {
	global $gBitSystem;
	$query = "select * from `".BIT_DB_PREFIX."blogs` order by `hits` desc";

	$result = $gBitSystem->mDb->query($query,array(),$limit,0);
	$ret = array();

	while ($res = $result->fetchRow()) {
		$aux["name"] = $res["title"];

		$aux["hits"] = $res["hits"];
		$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
		$ret[] = $aux;
	}

	$retval["data"] = $ret;
	$retval["title"] = tra("Most visited blogs");
	$retval["y"] = tra("Visits");
	return $retval;
}

function blog_ranking_top_active_blogs($limit) {
	global $gBitSystem;
	$query = "select * from `".BIT_DB_PREFIX."blogs` order by `activity` desc";

	$result = $gBitSystem->mDb->query($query,array(),$limit,0);
	$ret = array();

	while ($res = $result->fetchRow()) {
		$aux["name"] = $res["title"];

		$aux["hits"] = $res["activity"];
		$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
		$ret[] = $aux;
	}

	$retval["data"] = $ret;
	$retval["title"] = tra("Most active blogs");
	$retval["y"] = tra("Activity");
	return $retval;
}

function blog_ranking_last_posts($limit) {
	global $gBitSystem;
	$query = "select * from `".BIT_DB_PREFIX."blog_posts` order by `post_id` desc";

	$result = $gBitSystem->mDb->query($query,array(),$limit,0);
	$ret = array();

	while ($res = $result->fetchRow()) {
		$q = "select title, created from `".BIT_DB_PREFIX."blogs` where `blog_id`=";
		$q.= $res["blog_id"];
		$result2 = $gBitSystem->mDb->query($q,array(),$limit,0);
		$res2 = $result2->fetchRow();
		$aux["name"] = $res2["title"];
		$aux["hits"] = $gBitSystem->get_long_datetime($res2["created"]);
		$aux["href"] = BLOGS_PKG_URL.'view.php?blog_id=' . $res["blog_id"];
		$ret[] = $aux;
	}

	$retval["data"] = $ret;
	$retval["title"] = tra("Blogs last posts");
	$retval["y"] = tra("Post date");
	return $retval;
}
?>
