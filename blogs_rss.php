<?php
require_once( "../bit_setup_inc.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );
include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$gBitSystem->verifyPackage( 'rss' );
$gBitSystem->verifyPackage( 'blogs' );
$gBitUser->hasPermission( 'bit_p_read_blog' );

// feed info
$rss->title = $gBitSystem->getPreference( 'title_rss_blogs', $gBitSystem->mPrefs['siteTitle'] );
$rss->description = $gBitSystem->getPreference( 'desc_rss_blogs', $gBitSystem->mPrefs['siteTitle'].' - '.tra( 'RSS Feed' ) );

// check permission to view wiki pages
if( !$gBitUser->hasPermission( 'bit_p_read_blog' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	$blogPost = new BitBlogPost();
	$listHash['sort_mode'] = 'last_modified_desc';
	$listHash['max_records'] = $gBitSystem->getPreference( 'max_rss_blogs', 10 );
	$listHash['parse_data'] = TRUE;
	if( !empty($_REQUEST['blog_id'] ) ) {
		$listHash['blog_id'] = $_REQUEST['blog_id'];
	}
	$feeds = $blogPost->getList( $listHash );

	// get all the data ready for the feed creator
	foreach( $feeds['data'] as $feed ) {
		$item = new FeedItem();
		$item->title = $feed['title'];
		$item->link = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL.$blogPost->getDisplayUrl( $feed['post_id'] );
		$item->description = $feed['data'];

		$item->date = (int) $feed['last_modified'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $gBitUser->getDisplayName( FALSE, $feed );

		$item->descriptionTruncSize = $gBitSystem->getPreference( 'rssfeed_truncate', 500 );
		$item->descriptionHtmlSyndicated = true;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	$cacheFile = TEMP_PKG_PATH.'rss/blogs_'.$version.'.xml';
	$rss->useCached( $cacheFile ); // use cached version if age < 1 hour
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
