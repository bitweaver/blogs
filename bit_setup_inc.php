<?php
/**
 * @package blogs
 */

global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'blogs',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
	'service' => LIBERTY_SERVICE_BLOGS,
);
$gBitSystem->registerPackage( $registerHash );

/* Simple function to fetch and set all config values. */
function assignConfigs($x) {
	global $gBitSmarty, $gBitSystem;
	foreach ($x as $key)
		$gBitSmarty->assign($key, $gBitSystem->getConfig($key));
}

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

	$prefs = array(
					'home_blog',
					'blog_list_order',
					'blog_list_title',
					'blog_list_description',
					'blog_list_created',
					'blog_list_lastmodif',
					'blog_list_user',
					'blog_list_posts',
					'blog_list_visits',
					'blog_list_activity',
					'blog_list_user_as',
				);
	assignConfigs($prefs);

	require_once( 'BitBlog.php' );
	
	$gLibertySystem->registerService( LIBERTY_SERVICE_BLOGS, BLOGS_PKG_NAME, array(
		'users_register_function' => 'blogs_users_register',
	) );
}
?>
