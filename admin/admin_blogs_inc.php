<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/admin/admin_blogs_inc.php,v 1.19 2007/07/27 11:40:19 wjames5 Exp $

// get a list of blogs for the selection of the home blog
require_once( BLOGS_PKG_PATH.'BitBlog.php' );
$blog = new BitBlog();
$listHash['sort_mode'] = 'created_desc';
$blogList = $blog->getList( $listHash );
$gBitSmarty->assign_by_ref( 'blogList', $blogList );

if( !empty( $_REQUEST["set_blog_home"] )) {
	$blog_home = @BitBase::verifyId( $_REQUEST['blog_home'] ) ? $_REQUEST['blog_home'] : NULL;
	$gBitSystem->storeConfig( "blog_home", $blog_home, BLOGS_PKG_NAME );
	$gBitSmarty->assign( 'blog_home', $blog_home );
}

$formBlogLists = array(
	"blog_list_title" => array(
		'label' => 'Title',
	),
	"blog_list_description" => array(
		'label' => 'Description',
	),
	"blog_list_created" => array(
		'label' => 'Creation date',
	),
	"blog_list_lastmodif" => array(
		'label' => 'Last modification time',
	),
	"blog_list_user" => array(
		'label' => 'Creator',
		'note' => 'The creator of a particular blog.',
	),
	"blog_list_posts" => array(
		'label' => 'Posts',
		'note' => 'Number of posts submitted to any given blog.',
	),
	"blog_list_visits" => array(
		'label' => 'Visits',
		'note' => 'Number of times a given blog has been visited.',
	),
	/* TODO: Add back once activity is implemented
	"blog_list_activity" => array(
		'label' => 'Activity',
		'note' => 'This number is an indication of how active a given blog is. The number is calculated based on god knows what...',
	),
	*/
);
$gBitSmarty->assign( 'formBlogLists',$formBlogLists );

$formBlogFeatures = array(
	"blog_rankings" => array(
		'label' => 'Rankings',
		'note' => 'Enable the use of rankings based on page hits.',
	),
	"blog_posts_comments" => array(
		'label' => 'Blog Post Comments',
		'note' => 'Allow the addition of comments to blog posts.',
	),
	"blog_posts_autosplit" => array(
		'label' => 'Use 2 Text Fields To Auto Split Blog Posts',
		'note' => 'Display two text fields when editing a post, for intro and read more sections. Disabling will display one text field and requires use of ...split... to create a read more section',
	),
);
$gBitSmarty->assign( 'formBlogFeatures',$formBlogFeatures );

$formBlogInputs = array(
	"blog_top_post_count" => array(
		'label' => 'Top Post Count',
		'note' => 'How many posts per blog in the rankings should be shown.',
	),
);
$gBitSmarty->assign( 'formBlogInputs', $formBlogInputs );

$processForm = set_tab();

if( $processForm ) {
	$blogToggles = array_merge( $formBlogLists,$formBlogFeatures );
	foreach( $blogToggles as $item => $data ) {
		simple_set_toggle( $item, BLOGS_PKG_NAME );
	}

	// Lazy error handling to ensure numeric. TODO: Fix.
	$gBitSystem->storeConfig("blog_top_post_count", (isset( $_REQUEST["blog_top_post_count"]) && is_numeric($_REQUEST["blog_top_post_count"])) ? $_REQUEST["blog_top_post_count"] : "3");
	$gBitSystem->storeConfig("blog_posts_max_list", (isset( $_REQUEST["blog_posts_max_list"]) && is_numeric($_REQUEST["blog_posts_max_list"])) ? $_REQUEST["blog_posts_max_list"] : "10");
	$gBitSystem->storeConfig("blog_posts_comments", isset( $_REQUEST["blog_posts_comments"] ) ? 'y' : 'n', BLOGS_PKG_NAME );
	$gBitSystem->storeConfig("blog_list_order", $_REQUEST["blog_list_order"], BLOGS_PKG_NAME );
	$gBitSystem->storeConfig("blog_list_user_as", $_REQUEST["blog_list_user_as"], BLOGS_PKG_NAME );
	$gBitSystem->storeConfig("blog_posts_description_length", $_REQUEST["blog_posts_description_length"], BLOGS_PKG_NAME );	
	$gBitSystem->storeConfig("blog_posts_autosplit", isset( $_REQUEST["blog_posts_autosplit"] ) ? 'y' : 'n', BLOGS_PKG_NAME );	
	$gBitSmarty->assign('blog_list_order', $_REQUEST["blog_list_order"]);
	$gBitSmarty->assign('blog_list_user_as', $_REQUEST['blog_list_user_as']);
}

?>
