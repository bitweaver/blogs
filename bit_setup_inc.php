<?php
/**
 * @package blogs
 */

global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'blogs',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'blogs' ) ) {
	if( $gBitUser->hasPermission( 'p_blogs_admin' ) ) {
		$gBitUser->setPreference( 'p_blogs_create', TRUE );
		$gBitUser->setPreference( 'p_blogs_post', TRUE );
		$gBitUser->setPreference( 'p_blogs_view', TRUE );
	}

	if( $gBitUser->hasPermission( 'p_blogs_view' ) ) {
		$menuHash = array(
			'package_name'  => BLOGS_PKG_NAME,
			'index_url'     => BLOGS_PKG_URL.'index.php',
			'menu_template' => 'bitpackage:blogs/menu_blogs.tpl',
		);
		$gBitSystem->registerAppMenu( $menuHash );
	}

	$gBitSystem->registerNotifyEvent( array( "blog_post" => tra("An entry is posted to a blog") ) );

	$gBitSmarty->assign('home_blog', 0);
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
