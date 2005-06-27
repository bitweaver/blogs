<?php
/**
 * @package BitBlog
 */

	global $gContent, $smarty;

	if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
		$postId = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : NULL;
		$gContent = new BitBlogPost( $postId );
		$gContent->load();
		$comments_return_url = $_SERVER['PHP_SELF']."?post_id=$postId";
		$smarty->assign_by_ref( 'gContent', $gContent );
	}
?>
