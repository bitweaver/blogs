<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/blogs_rss.php,v 1.19 2007/01/07 10:48:30 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( "../bit_setup_inc.php" );

$gBitSystem->verifyPackage( 'rss' );
$gBitSystem->verifyPackage( 'blogs' );
$gBitSystem->verifyFeature( 'blogs_rss' );

require_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
require_once( RSS_PKG_PATH."rss_inc.php" );

// default feed info
$rss->title = $gBitSystem->getConfig( 'blogs_rss_title', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'Blogs' ) );
$rss->description = $gBitSystem->getConfig( 'blogs_rss_description', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'RSS Feed' ) );

// check permission to view wiki pages
if( !$gBitUser->hasPermission( 'p_blogs_view' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	// check if we want to use the cache file
	$cacheFile = TEMP_PKG_PATH.RSS_PKG_NAME.'/'.BLOGS_PKG_NAME.( !empty( $_REQUEST['blog_id'] ) ? "_".$_REQUEST['blog_id'] : "" ).'_'.$rss_version_name.'.xml';
	$rss->useCached( $rss_version_name, $cacheFile, $gBitSystem->getConfig( 'rssfeed_cache_time' ));

	$blogPost = new BitBlogPost();
	$listHash['sort_mode'] = 'last_modified_desc';
	$listHash['max_records'] = $gBitSystem->getConfig( 'blogs_rss_max_records', 10 );
	$listHash['parse_data'] = TRUE;
	if( !empty( $_REQUEST['blog_id'] ) ) {
		$listHash['blog_id'] = $_REQUEST['blog_id'];
	}
	$feeds = $blogPost->getList( $listHash );

	// adjust feed title to blog title
	if( !empty( $_REQUEST['blog_id'] ) && !empty( $feeds['data'] ) ) {
		$rss->title = $feeds['data'][0]['blogtitle'];
		$rss->description = $feeds['data'][0]['blogdescription'];
	}

	// set the rss link
	$rss->link = 'http://'.$_SERVER['HTTP_HOST'].BLOGS_PKG_URL.( !empty( $_REQUEST['blog_id'] ) ? "?blog_id=".$_REQUEST['blog_id'] : "" );

	// get all the data ready for the feed creator
	foreach( $feeds['data'] as $feed ) {
		$item = new FeedItem();
		$item->title = $blogPost->getTitle( $feed );
		$item->link = BIT_BASE_URI.$blogPost->getDisplayUrl( $feed['content_id'] );
		$item->description = $feed['parsed_data'];

		$item->date = ( int )$feed['last_modified'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $gBitUser->getDisplayName( FALSE, $feed );

		$item->descriptionTruncSize = $gBitSystem->getConfig( 'rssfeed_truncate', 5000 );
		$item->descriptionHtmlSyndicated = TRUE;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
