<?php

$tables = array(

'blog_posts' => "
	post_id I4 PRIMARY,
	content_id I4 NOTNULL,
	publish_date I4,
	expire_date I4,
	trackbacks_to X,
	trackbacks_from X
	CONSTRAINT '
		, CONSTRAINT `blog_posts_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
",

'blogs' => "
	blog_id I4 PRIMARY,
	content_id I4 NOTNULL,
	is_public C(1),
	max_posts I4,
	activity decimal(4,2),
	use_find C(1),
	use_title C(1),
	add_date C(1),
	add_poster C(1),
	allow_comments C(1)
	CONSTRAINT ', CONSTRAINT `blogs_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
",

'blogs_posts_map' => "
	post_content_id I4 NOTNULL,
	blog_content_id I4 NOTNULL,
	date_added I4,
	crosspost_note X
	CONSTRAINT ', CONSTRAINT `blogs_posts_map_post_ref` FOREIGN KEY (`post_content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
				, CONSTRAINT `blogs_posts_map_blog_ref` FOREIGN KEY (`blog_content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
",

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( BLOGS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( BLOGS_PKG_NAME, array(
	'description' => "A Blog is a web based journal or diary.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Indexes
$indices = array (
	'blog_posts_post_id_idx'    => array( 'table' => 'blog_posts', 'cols' => 'post_id',    'opts' => NULL ),
	'blogs_content_id_idx'      => array( 'table' => 'blogs',      'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
	'blog_posts_content_id_idx' => array( 'table' => 'blog_posts', 'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
);
/** @TODO - SPIDERR - following seems to cause time _decrease_ cause bigint on postgres. need more investigation
 *	'blog_posts_created_idx' => array( 'table' => 'blog_posts', 'cols' => 'created', 'opts' => NULL ),
 **/
$gBitInstaller->registerSchemaIndexes( BLOGS_PKG_NAME, $indices );

// ### Sequences
$sequences = array (
	'blogs_blog_id_seq'      => array( 'start' => 1 ),
	'blog_posts_post_id_seq' => array( 'start' => 1 )
);
$gBitInstaller->registerSchemaSequences( BLOGS_PKG_NAME, $sequences );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( BLOGS_PKG_NAME, array(
	array('p_blogs_create', 'Can create a blog', 'registered', BLOGS_PKG_NAME),
	array('p_blogs_create_is_public', 'Can create a public blog', 'editors', BLOGS_PKG_NAME),
	array('p_blogs_post', 'Can create a blog post', 'registered', BLOGS_PKG_NAME),
	array('p_blogs_update', 'Can update blogs and blog posts', 'editors', BLOGS_PKG_NAME),
	array('p_blogs_send_post', 'Can email a blog post', 'registered', BLOGS_PKG_NAME),
	array('p_blogs_admin', 'Can admin blogs', 'editors', BLOGS_PKG_NAME),
	array('p_blogs_view', 'Can read blogs', 'basic', BLOGS_PKG_NAME),
	array('p_blog_posts_read_future', 'Can view future dated posts', 'editors', BLOGS_PKG_NAME),
	array('p_blog_posts_read_expired', 'Can view expired posts', 'editors', BLOGS_PKG_NAME)
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( BLOGS_PKG_NAME, array(
	array( BLOGS_PKG_NAME, 'blog_list_activity','y'),
	array( BLOGS_PKG_NAME, 'blog_list_created','y'),
	array( BLOGS_PKG_NAME, 'blog_list_description','y'),
	array( BLOGS_PKG_NAME, 'blog_list_lastmodif','y'),
	array( BLOGS_PKG_NAME, 'blog_list_order','created_desc'),
	array( BLOGS_PKG_NAME, 'blog_list_posts','y'),
	array( BLOGS_PKG_NAME, 'blog_list_title','y'),
	array( BLOGS_PKG_NAME, 'blog_list_user','n'),
	array( BLOGS_PKG_NAME, 'blog_list_visits','y'),
	array( BLOGS_PKG_NAME, 'blog_categ','n'),
	array( BLOGS_PKG_NAME, 'blog_parent_categ',0),
	array( BLOGS_PKG_NAME, 'blog_posts_comments','n'),
	array( BLOGS_PKG_NAME, 'blog_rankings','y'),
	array( BLOGS_PKG_NAME, 'blog_list_user_as', 'text'),
	array( BLOGS_PKG_NAME, 'blog_posts_description_length', '500'),
	array( BLOGS_PKG_NAME, 'blog_posts_max_list','10'),
) );

// ### User Preferences Set In This Package
/** These are mentioned here for reference to understand how the package works
 * They are not to be configured here!
 *
 * user_blog_posts_use_title, default y			lets the user toggle to use a typed title for their posts or automatically use a date
 * user_blog_posts_allow_comments, default y	lets a user toggle comments on their blog posts
 * user_blog_description_inc, default n			lets a user include their personal page at the top of their blog
 * blog_posts_autosplit, default n				automatically uses two fields for editing posts
 *
 * @todo need an admin pref to override allow_comments option 
 * @todo need an admin pref to override description option 
 *
 **/

if(defined('RSS_PKG_NAME')) {
	$gBitInstaller->registerPreferences( BLOGS_PKG_NAME, array(
		array( RSS_PKG_NAME, BLOGS_PKG_NAME.'_rss', 'y'),
	));
}

// ### Register content types
$gBitInstaller->registerContentObjects( BLOGS_PKG_NAME, array( 
	'BitBlog'=>BLOGS_PKG_CLASS_PATH.'BitBlog.php',
	'BitBlogPost'=>BLOGS_PKG_CLASS_PATH.'BitBlogPost.php'
));

// Requirements
$gBitInstaller->registerRequirements( BLOGS_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.4' ),
));

