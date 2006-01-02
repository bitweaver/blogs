<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/blogs_rss.php,v 1.1.1.1.2.10 2006/01/02 15:59:54 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( "../bit_setup_inc.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );
include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );

$gBitSystem->verifyPackage( 'rss' );
$gBitSystem->verifyPackage( 'blogs' );

// default feed info
$rss->title = $gBitSystem->getPreference( 'title_rss_blogs', $gBitSystem->mPrefs['siteTitle'].' - '.tra( 'Blogs' ) );
$rss->description = $gBitSystem->getPreference( 'desc_rss_blogs', $gBitSystem->mPrefs['siteTitle'].' - '.tra( 'RSS Feed' ) );

// check permission to view wiki pages
if( !$gBitUser->hasPermission( 'bit_p_read_blog' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	// check if we want to use the cache file
	$cacheFile = TEMP_PKG_PATH.RSS_PKG_NAME.'/'.BLOGS_PKG_NAME.'_'.$version.'.xml';
	$rss->useCached( $cacheFile ); // use cached version if age < 1 hour

	$blogPost = new BitBlogPost();
	$listHash['sort_mode'] = 'last_modified_desc';
	$listHash['max_records'] = $gBitSystem->getPreference( 'max_rss_blogs', 10 );
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

	// get all the data ready for the feed creator
	foreach( $feeds['data'] as $feed ) {
		$item = new FeedItem();
		$item->title = $blogPost->getTitle( $feed );
		$item->link = BIT_BASE_URI.$blogPost->getDisplayUrl( $feed['content_id'] );
		$item->description = $feed['parsed_data'];

		$item->date = ( int )$feed['last_modified'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $gBitUser->getDisplayName( FALSE, $feed );

		$item->descriptionTruncSize = $gBitSystem->getPreference( 'rssfeed_truncate', 5000 );
		$item->descriptionHtmlSyndicated = TRUE;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
