<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/admin/pump_blogs_inc.php,v 1.1 2007/07/09 13:16:10 squareing Exp $
 * @package install
 * @subpackage pumps
 */

// keep in mind that most recent blog posts are show at the top of the blog
/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

$pageHash = array(
		'fSavePage' => TRUE,
		'format_guid' => 'tikiwiki',
		'title' => 'bitweaver Blog',
		'edit' => 'Sample Blog added during the installation process',
		'use_title' => 'y',
		'max_posts' => '10',
		'use_title' => 'on',
		'ust_find' => 'on',		
		'allow_comments' => 'on',
);

$gBlog = new BitBlog();
if( $gBlog->store( $pageHash ) ) {
	$blogPostHash = array(
		array(
			'fSavePage' => TRUE,
			'format_guid' => 'tikiwiki',
			'blog_content_id' => array($gBlog->mContentId),
			'trackback' => NULL,
			'title' => 'Lorem Ipsum',
			'edit' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vestibulum molestie lectus quis elit. Suspendisse scelerisque augue vitae ipsum. Maecenas quis enim. Suspendisse at turpis sed sem ullamcorper aliquam. Aliquam pede ligula, auctor vitae, interdum eget, aliquam vitae, magna. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam varius feugiat neque. Pellentesque varius. Fusce a mi in nulla porta aliquam. Morbi scelerisque, turpis quis sodales blandit, libero lorem faucibus dui, sit amet lacinia sem pede in quam. Pellentesque tempor suscipit sem. Nam sed augue. 

	Nulla blandit. Vestibulum tempor ullamcorper nulla. Pellentesque varius lectus nec urna. Proin volutpat pede in eros. Mauris sit amet pede in neque nonummy congue. Ut vitae felis. Nunc lacinia. Fusce placerat faucibus orci. Ut vel libero et nisl hendrerit pretium. Sed quis quam id augue porta tempus. Nullam ante risus, blandit sed, eleifend eget, imperdiet a, sapien. Vestibulum libero. Phasellus viverra nonummy dui. In ultrices. Pellentesque imperdiet eros vel urna. Maecenas fringilla rutrum sem. Nullam lacinia, ipsum ut euismod scelerisque, mi dolor faucibus eros, in semper magna lorem non risus. Cras nec elit. Etiam egestas. Praesent placerat diam. 

	Donec nec velit. Mauris scelerisque vestibulum ante. Nulla congue commodo lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Aenean posuere, magna sit amet pharetra consectetuer, erat sapien ultricies elit, sit amet sollicitudin sem nibh vitae erat. Proin commodo feugiat nunc. Proin in eros. Fusce nec ante. Nulla convallis mi quis arcu. Duis quam. Cras arcu sem, posuere ut, sodales sit amet, blandit eu, orci. Sed tincidunt egestas est. Curabitur tempus, ipsum ut blandit accumsan, odio tellus egestas ipsum, sit amet cursus lacus velit et est. Nulla at sem. Aliquam quam purus, bibendum ut, tristique sollicitudin, interdum eget, ante. Nulla a ligula. Mauris scelerisque sem a odio. Quisque a nunc sed risus eleifend rutrum. Maecenas vitae lacus.
	'),
		array(
			'fSavePage' => TRUE,
			'format_guid' => 'tikiwiki',
			'blog_content_id' => array($gBlog->mContentId),
			'trackback' => NULL,
			'title' => 'Another Blog',
			'edit' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vestibulum molestie lectus quis elit. Suspendisse scelerisque augue vitae ipsum. Maecenas quis enim. Suspendisse at turpis sed sem ullamcorper aliquam. Aliquam pede ligula, auctor vitae, interdum eget, aliquam vitae, magna. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam varius feugiat neque. Pellentesque varius. Fusce a mi in nulla porta aliquam. Morbi scelerisque, turpis quis sodales blandit, libero lorem faucibus dui, sit amet lacinia sem pede in quam. Pellentesque tempor suscipit sem. Nam sed augue. 

	Nulla blandit. Vestibulum tempor ullamcorper nulla. Pellentesque varius lectus nec urna. Proin volutpat pede in eros. Mauris sit amet pede in neque nonummy congue. Ut vitae felis. Nunc lacinia. Fusce placerat faucibus orci. Ut vel libero et nisl hendrerit pretium. Sed quis quam id augue porta tempus. Nullam ante risus, blandit sed, eleifend eget, imperdiet a, sapien. Vestibulum libero. Phasellus viverra nonummy dui. In ultrices. Pellentesque imperdiet eros vel urna. Maecenas fringilla rutrum sem. Nullam lacinia, ipsum ut euismod scelerisque, mi dolor faucibus eros, in semper magna lorem non risus. Cras nec elit. Etiam egestas. Praesent placerat diam. 
	'),
		array(
			'fSavePage' => TRUE,
			'format_guid' => 'tikiwiki',
			'blog_content_id' => array($gBlog->mContentId),
			'trackback' => NULL,
			'title' => 'Welcome',
			'edit' => 'This is the blogs package of bitweaver. A blog is short for __web log__ and is basically a journal that is available on the web. The activity of updating a blog is "blogging" and someone who keeps a blog is a "blogger." Blogs are typically updated daily using software that allows people with little or no technical background to update and maintain the blog. Postings on a blog are almost always arranged in cronological order with the most recent additions featured most prominantly.

	If you have added the __Package Wiki__ to your isntallation and added the wiki sample data, you will notice that it is possible to link to those pages using simple wiki words such as bitweaverGlossary.
	'),
	);
	foreach( $blogPostHash as $blogPost ) {
		$newPost = new BitBlogPost( NULL );
		if( $newPost->store( $blogPost ) ) {
			$pumpedData['Blog'][] = $blogPost['title'];
		} else {
			$error = $newPost->mErrors;
			$gBitSmarty->assign( 'error',$error );
		}
	}
}
?>
