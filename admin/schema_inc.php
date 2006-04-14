<?php

$tables = array(

'blog_posts' => "
	post_id I4 PRIMARY,
	content_id I4 NOTNULL,
	blog_id I4 NOTNULL,
	trackbacks_to X,
	trackbacks_from X
",

'blogs' => "
	blog_id I4 AUTO PRIMARY,
	user_id I4 NOTNULL,
	created I8 NOTNULL,
	last_modified I8 NOTNULL,
	title C(200),
	description X,
	public_blog C(1),
	posts I4,
	max_posts I4,
	hits I4,
	activity decimal(4,2),
	heading X,
	use_find C(1),
	use_title C(1),
	add_date C(1),
	add_poster C(1),
	allow_comments C(1)
"

);

global $gBitInstaller;

$gBitInstaller->makePackageHomeable(BLOGS_PKG_NAME);

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( BLOGS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( BLOGS_PKG_NAME, array(
	'description' => "A Blog is a web based journal or diary.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'beta',
	'dependencies' => '',
) );

// ### Indexes
$indices = array (
	'blog_posts_blog_id_idx' => array( 'table' => 'blog_posts', 'cols' => 'blog_id', 'opts' => NULL ),
	'blogs_title_idx' => array( 'table' => 'blogs', 'cols' => 'title', 'opts' => NULL ),
	'blogs_hits_idx' => array( 'table' => 'blogs', 'cols' => 'hits', 'opts' => NULL ),
	'blogs_user_id_idx' => array( 'table' => 'blogs', 'cols' => 'user_id', 'opts' => NULL )
);
// TODO - SPIDERR - following seems to cause time _decrease_ cause bigint on postgres. need more investigation
//	'blog_posts_created_idx' => array( 'table' => 'blog_posts', 'cols' => 'created', 'opts' => NULL ),
$gBitInstaller->registerSchemaIndexes( BLOGS_PKG_NAME, $indices );

// ### Sequences
$sequences = array (
	'blog_posts_post_id_seq' => array( 'start' => 1 )
);
$gBitInstaller->registerSchemaSequences( BLOGS_PKG_NAME, $sequences );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( BLOGS_PKG_NAME, array(
	array('p_blogs_create', 'Can create a blog', 'registered', BLOGS_PKG_NAME),
	array('p_blogs_create_public_blog', 'Can create a public blog', 'editors', BLOGS_PKG_NAME),
	array('p_blogs_post', 'Can post to a blog', 'registered', BLOGS_PKG_NAME),
	array('p_blogs_admin', 'Can admin blogs', 'editors', BLOGS_PKG_NAME),
	array('p_blogs_view', 'Can read blogs', 'basic', BLOGS_PKG_NAME)
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
	array( RSS_PKG_NAME, 'rss_'.BLOGS_PKG_NAME, 'y'),
) );

?>
