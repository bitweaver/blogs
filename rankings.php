<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/rankings.php,v 1.3 2005/08/01 18:40:04 squareing Exp $

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

include_once( KERNEL_PKG_PATH.'rank_lib.php' );

$gBitSystem->verifyPackage( 'blogs' );

if ($feature_blog_rankings != 'y') {
	$gBitSmarty->assign('msg', tra("This feature is disabled").": feature_blog_rankings");

	$gBitSystem->display( 'error.tpl' );
	die;
}

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

$rk = $ranklib->$which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"] = $rk["y"];
$rankings[] = $rank;

$gBitSmarty->assign_by_ref('rankings', $rankings);
$gBitSmarty->assign('rpage', 'rankings.php');


// Display the template
$gBitSystem->display( 'bitpackage:kernel/ranking.tpl');

?>
