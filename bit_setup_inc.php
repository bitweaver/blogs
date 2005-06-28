<?php
/**
 * @package blogs
 */

global $gBitSystem, $smarty, $bit_p_blog_admin;
$gBitSystem->registerPackage( 'blogs', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'blogs' ) ) {

	$gBitSystem->registerAppMenu( 'blogs', 'Blogs', BLOGS_PKG_URL.'index.php', 'bitpackage:blogs/menu_blogs.tpl', 'blogs');

	$gBitSystem->registerNotifyEvent( array( "blog_post" => tra("An entry is posted to a blog") ) );

    $smarty->assign('home_blog', 0);
    $smarty->assign('blog_comments_default_ordering', 'points_desc');
    $smarty->assign('blog_comments_per_page', 10);
    $smarty->assign('blog_spellcheck', 'n');
    $smarty->assign('blog_list_order', 'created_desc');
    $smarty->assign('blog_list_title', 'y');
    $smarty->assign('blog_list_description', 'y');
    $smarty->assign('blog_list_created', 'y');
    $smarty->assign('blog_list_lastmodif', 'y');
    $smarty->assign('blog_list_user', 'y');
    $smarty->assign('blog_list_posts', 'y');
    $smarty->assign('blog_list_visits', 'y');
    $smarty->assign('blog_list_activity', 'y');
    $smarty->assign('blog_list_user', 'text');

}

?>
