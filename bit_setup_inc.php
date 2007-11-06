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
		// this probably has no effect since it doesn't update the users permissions
		$gBitUser->setPreference( 'p_blogs_create', TRUE );
		$gBitUser->setPreference( 'p_blogs_post', TRUE );
		$gBitUser->setPreference( 'p_blogs_view', TRUE );
	}

	if( $gBitUser->hasPermission( 'p_blogs_view' ) ) {
		$menuHash = array(
			'package_name'       => BLOGS_PKG_NAME,
			'index_url'          => BLOGS_PKG_URL.'index.php',
			'menu_template'      => 'bitpackage:blogs/menu_blogs.tpl',
			'admin_comments_url' => KERNEL_PKG_URL.'admin/index.php?page=blogs',
		);
		$gBitSystem->registerAppMenu( $menuHash );
		
		$gLibertySystem->registerService( LIBERTY_SERVICE_BLOGS, BLOGS_PKG_NAME, array(
			'module_display_function'  => 'blogs_module_display',
		) );
	}

	$gBitSystem->registerNotifyEvent( array( "blog_post" => tra("An entry is posted to a blog") ) );

	require_once( 'BitBlog.php' );
}
?>
