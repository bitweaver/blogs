<?php
global $gBitSmarty, $gBlog, $gBitSystem, $gQueryUserId, $moduleParams;
extract( $moduleParams );

include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$blogPost = new BitBlogPost();
if( empty( $gContent )) {
	$gBitSmarty->assign_by_ref( 'gContent', $blogPost );
}

// various options might be set in module_params
$listHash = $module_params;
$listHash = array(
	'max_records'   => $module_rows,
	'parse_data'    => TRUE,
	'load_comments' => TRUE,
);

if( empty( $listHash['user_id'] )) {
	if( !empty( $gQueryUserId )) {
		$listHash['user_id'] = $gQueryUserId;
	} elseif( $_REQUEST['user_id'] ) {
		$listHash['user_id'] = $_REQUEST['user_id'];
	}
}
$blogPosts = $blogPost->getList( $listHash );

$gBitSmarty->assign_by_ref( 'gQueryUserId', $listHash['user_id'] );
$gBitSmarty->assign_by_ref( 'blogPosts', $blogPosts["data"] );
$gBitSmarty->assign( 'listInfo', $blogPosts['listInfo'] );
$gBitSmarty->assign( 'descriptionLength', $gBitSystem->getConfig( 'blog_posts_description_length', 500 ));
$gBitSmarty->assign( 'showDescriptionsOnly', TRUE );
$gBitSmarty->assign( 'showBlogTitle', 'y' );
?>
