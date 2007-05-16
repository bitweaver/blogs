<?php
/**
 * @package blogs
 * @subpackage functions
 */

	global $gContent; //, $gBitSmarty;
	require_once( BLOGS_PKG_PATH.'BitBlogPost.php');
	require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );
	
	if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
		// if blog_id supplied, use that
		if( @BitBase::verifyId( $_REQUEST['post_id'] ) ) {
			$gContent = new BitBlogPost( $_REQUEST['post_id'] );
			$gContent->load();
		} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
			$gContent = new BitBlogPost( NULL, $_REQUEST['content_id'] );
			$gContent->load();
		} else {
			$gContent = new BitBlogPost();
		}
	
		$gBitSmarty->assign_by_ref( 'gContent', $gContent );
	}




// MOVE THIS TO display_bitblogpost_inc.php -wjames5
/*
global $gContent_previous;

if (!empty( $gContent) && !empty( $gContent->mInfo['blog_id'] ) ) {
	# get previos post if it exists
	$blog_id = $gContent->mInfo['blog_id'];
	$max_records = 1;
	$sort_mode = 'post_id_desc';
	$post_id = $gContent->mInfo['post_id'];
	$listHash = array();    
	$listHash['blog_id'] = $blog_id;
	$listHash['max_records'] = $max_records;
	$listHash['sort_mode'] = $sort_mode;
	$listHash['parse_data'] = TRUE;
	$listHash['load_comments'] = FALSE;
	$listHash['page'] = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);
	$listHash['offset'] = (!empty($_REQUEST['offset']) ? $_REQUEST['offset'] : 0);
	$listHash['offset'] = 0;
	$listHash['post_id_lt'] = $post_id;


	$gContent_previous = new BitBlogPost();
	$blogPosts = $gContent_previous->getList( $listHash );

#	echo "blogPosts=" . serialize($blogPosts);
	
	$blogposts_data = $blogPosts['data'];
	$gContent_previous = NULL;
	if (count($blogposts_data) > 0) {
		$blogpost = $blogposts_data[0];
		
	#	echo "blogPosts=" . serialize($blogPosts) . "\n\n<p>\n\n";
		$gContent_previous = new BitBlogPost($blogpost['post_id'],$blogpost['content_id']);
		$gContent_previous->load();

	#	echo "gContent_previous=" . serialize($gContent_previous);
#		echo "Previous title=" . $blogpost['title'];
		}

	$gBitSmarty->assign_by_ref( 'gContent_previous', $gContent_previous );


	# get next post if it exists
	$blog_id = $gContent->mInfo['blog_id'];
	$max_records = 1;
	$sort_mode = 'post_id_asc';
	$post_id = $gContent->mInfo['post_id'];
	$listHash = array();    
	$listHash['blog_id'] = $blog_id;
	$listHash['max_records'] = $max_records;
	$listHash['sort_mode'] = $sort_mode;
	$listHash['parse_data'] = TRUE;
	$listHash['load_comments'] = FALSE;
	$listHash['page'] = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);
	$listHash['offset'] = (!empty($_REQUEST['offset']) ? $_REQUEST['offset'] : 0);
	$listHash['offset'] = 0;
	$listHash['post_id_gt'] = $post_id;


	$gContent_next = new BitBlogPost();
	$blogPosts = $gContent_next->getList( $listHash );
#	echo "blogPosts=" . serialize($blogPosts);
	
	$blogposts_data = $blogPosts['data'];
	$gContent_next = NULL;
	if (count($blogposts_data) > 0) {
		$blogpost = $blogposts_data[0];
		
	#	echo "blogPosts=" . serialize($blogPosts) . "\n\n<p>\n\n";
		$gContent_next = new BitBlogPost($blogpost['post_id'],$blogpost['content_id']);
		$gContent_next->load();

	#	echo "gContent_next=" . serialize($gContent_next);
#		echo "Next title=" . $blogpost['title'];
	}

	$gBitSmarty->assign_by_ref( 'gContent_next', $gContent_next );
}
*/
?>
