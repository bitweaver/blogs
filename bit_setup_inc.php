<?php
/**
 * @package blogs
 */

global $gBitSystem, $gBitUser, $gBitSmarty, $bit_p_blog_admin;
$gBitSystem->registerPackage( 'blogs', dirname( __FILE__).'/' );

if( $gBitSystem->isPackageActive( 'blogs' ) ) {
	if ($gBitUser->hasPermission( 'bit_p_read_blog' )) {
		$gBitSystem->registerAppMenu( 'blogs', 'Blogs', BLOGS_PKG_URL.'index.php', 'bitpackage:blogs/menu_blogs.tpl', 'blogs');
	}

	$gBitSystem->registerNotifyEvent( array( "blog_post" => tra("An entry is posted to a blog") ) );

    $gBitSmarty->assign('home_blog', 0);
    $gBitSmarty->assign('blog_spellcheck', 'n');
    $gBitSmarty->assign('blog_list_order', 'created_desc');
    $gBitSmarty->assign('blog_list_title', 'y');
    $gBitSmarty->assign('blog_list_description', 'y');
    $gBitSmarty->assign('blog_list_created', 'y');
    $gBitSmarty->assign('blog_list_lastmodif', 'y');
    $gBitSmarty->assign('blog_list_user', 'y');
    $gBitSmarty->assign('blog_list_posts', 'y');
    $gBitSmarty->assign('blog_list_visits', 'y');
    $gBitSmarty->assign('blog_list_activity', 'y');
    $gBitSmarty->assign('blog_list_user', 'text');

}

?>
