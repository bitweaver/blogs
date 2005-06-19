<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/blogs_rss.php,v 1.1 2005/06/19 03:57:41 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once( '../bit_setup_inc.php' );
require_once( KERNEL_PKG_PATH.'BitBase.php' );
include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

global $gBitSystem;

if ($rss_blogs != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once( RSS_PKG_PATH.'rss_error.php' );
}

if (!$gBitUser->hasPermission( 'bit_p_read_blog' )) {
	$errmsg=tra("Permission denied you cannot view this section");
	require_once( RSS_PKG_PATH.'rss_error.php' );
}


if (!empty($_REQUEST['blog_id'])) {
	$blogInfo = $gBlog->get_blog($_REQUEST['blog_id']);
	$title = $gBitSystem->getPreference( 'title_rss_blog', "Tiki RSS feed for " ).$blogInfo['title'];
	$desc = $gBitSystem->getPreference( 'desc_rss_blog', "Last modifications to the Blog: " ).$blogInfo['description'];
} else {	
	$title = "Blogs RSS feed for ".$gBitSystem->getPreference("siteTitle","Tiki");
	$desc = $gBitSystem->getPreference( 'desc_rss_blog', "Last modifications to the Blogs" );
}
$now = date("U");
$id = "blog_id";
$desc_id = "parsed_data";
$dateId = "created";
$readrepl = "view_post.php?$id=";

require( RSS_PKG_PATH.'rss_read_cache.php' );

if ($output == "EMPTY") {
	$blogPost = new BitBlogPost();
	$listHash['sort_mode'] = $dateId.'_desc';
	$listHash['max_records'] = $gBitSystem->getPreference( 'max_rss_blogs', 10 );
	$listHash['parse_data'] = TRUE;
	if (!empty($_REQUEST['blog_id'])) {
		$listHash['blog_id'] = $_REQUEST['blog_id'];
	}

	$changes = $blogPost->getList( $listHash );
	$output="";
}

require( RSS_PKG_PATH.'rss.php' );

?>
