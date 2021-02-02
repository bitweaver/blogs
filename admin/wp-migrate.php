<?php
/*
 * This migration script is far from handling everything that word press
 * can store. It does what is needed for MY site but may NOT work
 * for yours at all. ~ nickpalmer
 *
 * Needs work to handle annonymous comment.
 * Needs work to handle attached images.
 * Needs work on handling errors better.
 * Needs work to handle mapping to groups.
 *
 * For large wordpress databases we may need to be able to run stages
 * storing the hashes in the database instead of in memory.
 * I leave such extensions for future authors or somebody with
 * bags of cash for me. :)
 *
 * You need to have liberty_allow_change_owner on for post ownership to work right. Otherwise all posts will be owned by admin.
 */

require_once( '../../kernel/setup_inc.php' );

//require_once(USERS_PKG_PATH.'RoleUser.php');
require_once(BLOGS_PKG_CLASS_PATH.'BitBlog.php');
require_once(BLOGS_PKG_CLASS_PATH.'BitBlogPost.php');
require_once(LIBERTY_PKG_PATH.'LibertyComment.php');

$gBitSystem->verifyPermission( 'p_admin' );

$_SESSION['captcha_verified'] = TRUE;

// To Run from the command line uncomment and set the right value here.
# $_REQUEST['wp_config'] = "/path/to/wordpress/";

if (isset($_REQUEST['wp_config'])) {
	$config = $_REQUEST['wp_config']."/wp-config.php";
	$format = $_REQUEST['wp_config']."/wp-includes/functions-formatting.php";
	if (file_exists($config)) {
		require_once($config);
		require_once($format);
		migrate_wp();
	} else {
		$errors['error'] = tra("The config file and format-function file do not exist.");
		$gBitSmarty->assign('errors', $errors);
	}
	$gBitSmarty->assign('wp_config', $_REQUEST['wp_config']);
}
$gBitSystem->display("bitpackage:blogs/wp-migrate.tpl", tra("WordPress Migrate"), array( 'display_mode' => 'admin' ));
die;

function migrate_wp() {
	global $gBitSmarty;
	global $wpdb;
	global $gErrorMap;
	global $gBitSystem;

	if (empty($wpdb->table_prefix)) {
		$wpdb->table_prefix = '';
	}

	setup_migration();
	migrate_wp_users();
	migrate_wp_categories();
	migrate_wp_posts();
	migrate_wp_post_map();
	migrate_wp_comments();
	$gBitSystem->storeConfig('blogs_wp_migration', 'y', 'blogs');

	$errors['success'] = tra("Your migration is complete.");
	$gBitSmarty->assign('errorMap', $gErrorMap);
	$gBitSmarty->assign('errors', $errors);
}

function setup_migration() {
	global $gBitSystem, $gUserMap, $gBlogMap, $gPostMap, $gCommentMap, $gMaxUser, $gMaxBlog, $gMaxPost, $gMaxComment;

	if ($gBitSystem->getConfig('blogs_wp_migration', 'n') == 'n') {
		$tables = array(
			'blogs_wp_users' => "wp_id INT PRIMARY, user_id INT NOTNULL",
			'blogs_wp_blogs' => "wp_id INT PRIMARY, blog_id INT NOTNULL",
			'blogs_wp_posts' => "wp_id INT PRIMARY, post_id INT NOTNULL",
			'blogs_wp_comments' => "wp_id INT PRIMARY, comment_id INT NOTNULL",
			);
		$gBitSystem->mDb->createTables($tables);
		$gUserMap = array();
		$gBlogMap = array();
		$gPostMap = array();
		$gCommentMap = array();
		$gMaxUser = -1;
		$gMaxBlog = -1;
		$gMaxPost = -1;
		$gMaxComment = -1;
	}
	else {
		$sql = "SELECT wp_id, user_id FROM `".BIT_DB_PREFIX."blogs_wp_users`";
		$gUserMap = $gBitSystem->mDb->getAssoc($sql);
		$sql = "SELECT max(wp_id) FROM `".BIT_DB_PREFIX."blogs_wp_users`";
		$gMaxUser = $gBitSystem->mDb->getOne($sql);

		$sql = "SELECT wp_id, blog_id FROM `".BIT_DB_PREFIX."blogs_wp_blogs`";
		$gBlogMap = $gBitSystem->mDb->getAssoc($sql);
		$sql = "SELECT max(wp_id) FROM `".BIT_DB_PREFIX."blogs_wp_blogs`";
		$gMaxBlog = $gBitSystem->mDb->getOne($sql);

		$sql = "SELECT wp_id, post_id FROM `".BIT_DB_PREFIX."blogs_wp_posts`";
		$gPostMap = $gBitSystem->mDb->getAssoc($sql);
		$sql = "SELECT max(wp_id) FROM `".BIT_DB_PREFIX."blogs_wp_posts`";
		$gMaxPost = $gBitSystem->mDb->getOne($sql);

		$sql = "SELECT wp_id, comment_id FROM `".BIT_DB_PREFIX."blogs_wp_comments`";
		$gCommentMap = $gBitSystem->mDb->getAssoc($sql);
		$sql = "SELECT max(wp_id) FROM `".BIT_DB_PREFIX."blogs_wp_comments`";
		$gMaxComment = $gBitSystem->mDb->getOne($sql);
	}
}

function migrate_wp_users() {
	global $wpdb, $gBitSystem, $gBitSmarty, $gUserMap, $gErrorMap, $gMaxUser;

	// Get everybody with a post
	$query = "select distinct(u.ID) from ".$wpdb->table_prefix."wp_users u INNER JOIN ".$wpdb->table_prefix."wp_comments c ON (u.ID = c.user_id) WHERE u.ID != 1";
	$post_users = $wpdb->get_results($query, ARRAY_A);
	// Get everybody with a comment
	$query = "select distinct(u.ID) from ". $wpdb->table_prefix."wp_users u INNER JOIN ".$wpdb->table_prefix."wp_posts p ON (u.ID = p.post_author) WHERE u.ID != 1";
	$comment_users = $wpdb->get_results($query, ARRAY_A);

	$users = array();
	foreach ($post_users as $key => $data) {
		$users[$data['ID']] = $data['ID'];
	}
	foreach ($comment_users as $key => $data) {
		$users[$data['ID']] = $data['ID'];
	}

	// Get info on everybody except the admin
	$query = "select ID, user_login AS login, user_pass AS password, display_name AS real_name, user_email AS email, user_registered, user_pass AS hash from ".$wpdb->table_prefix."wp_users WHERE ID != 1 AND ID IN (".implode(",", $users).") AND ID > $gMaxUser";

	$user_data = $wpdb->get_results($query);

	//  vd($user_data);
	if (!empty($user_data)) {
		foreach($user_data as $data) {
			$bu = new BitUser();
			$pParamHash = array();
			// Strip out characters that bitweaver doesn't support in logins.
			preg_match_all( '/[A-Za-z0-9_-]*/', $data->login, $matches);
			//    vd($matches);
			$pParamHash['login'] = implode("", $matches[0]);
			$pParamHash['password'] = $gBitSystem->genPass();
			$pParamHash['hash'] = $data->hash;
			$pParamHash['real_name'] = $data->real_name;
			$pParamHash['email'] = $data->email;
			// Mash user_registered into the right format
			$pParamHash['registration_date'] = $gBitSystem->mServerTimestamp->getTimestampFromIso($data->user_registered);
			//  vd($pParamHash);
			$bu->register($pParamHash);
			if (empty($bu->mErrors)) {
				//      vd($bu->mUserId);
				$gUserMap[$data->ID] = $bu->mUserId;
				$gBitSystem->mDb->query("UPDATE `".BIT_DB_PREFIX."users_users` SET hash = ? where user_id = ?", array($pParamHash['hash'], $bu->mUserId));
			} else {
				$gErrorMap[] = array('error' => "User ID: ".$pParamHash['login']." : ".implode(', ', $bu->mErrors));
				//      vd($bu->mErrors);
			}
		}
	}
	//  vd("Map.");
	//  vd($gUserMap);
	$sql = "INSERT INTO `".BIT_DB_PREFIX."blogs_wp_users` (`wp_id`, `user_id`) VALUES (?, ?)";
	foreach($gUserMap as $wp_id => $user_id) {
		if ($wp_id > $gMaxUser) {
			$gBitSystem->mDb->query($sql, array($wp_id, $user_id));
		}
	}
}

function migrate_wp_categories() {
	global $wpdb, $gBitSystem, $gBitSmarty, $gUserMap, $gErrorMap, $gBlogMap, $gMaxBlog;

	// Get info on categories
	$query = "select * from ".$wpdb->table_prefix."wp_categories WHERE cat_ID > $gMaxBlog";

	$blog_data = $wpdb->get_results($query);

	if (!empty($blog_data)) {
		foreach ($blog_data as $blog) {
			//    vd($blog);
			$b = new BitBlog();
			$pParamHash = array();
			$pParamHash['title'] = $blog->cat_name;
			$pParamHash['use_title'] = 'y';

			/** @TODO : Map Posts level to user group.
			  * @TODO : Make this options in the prep.
			  **/
			$pParamHash['is_public'] = 'y';
			$pParamHash['allow_comments'] = 'y';
			$pParamHash['max_posts'] = 10;
			$pParamHash[LIBERTY_TEXT_AREA] = $blog->category_description;
			//    vd($pParamHash);
			$b->store($pParamHash);
			if (empty($b->mErrors)) {
				$gBlogMap[$blog->cat_ID] = $b->mContentId;
			} else {
				$gErrorMap[] = "Blog: ".$blog->cat_name." : " . $b->mErrors;
			}
		}
	}
	//  vd($gBlogMap);
	$sql = "INSERT INTO `".BIT_DB_PREFIX."blogs_wp_blogs` (`wp_id`, `blog_id`) VALUES (?, ?)";
	foreach($gBlogMap as $wp_id => $blog_id) {
		if ($wp_id > $gMaxBlog) {
			$gBitSystem->mDb->query($sql, array($wp_id, $blog_id));
		}
	}
}

function migrate_wp_posts() {
	global $wpdb, $gBitSystem, $gBitSmarty, $gUserMap, $gErrorMap, $gPostMap, $gMaxPost;

	// Get info on categories
	$query = "select * from ".$wpdb->table_prefix."wp_posts WHERE ID > $gMaxPost";

	$posts = $wpdb->get_results($query);

	if (!empty($posts)) {
		foreach ($posts as $post) {
			$pParamHash = array();
			$pParamHash['data'] = wptexturize(convert_chars(wpautop($post->post_content)));
			$pParamHash['title'] = $post->post_title;
			if ($post->post_status == 'draft') {
				$pParamHash['content_status'] = -5;
			} else {
				$pParamHash['content_status'] = 50;
			}
			$pParamHash['publish_date'] = $gBitSystem->mServerTimestamp->getTimestampFromIso($post->post_date_gmt);
			$pParamHash['last_modified'] = $gBitSystem->mServerTimestamp->getTimestampFromIso($post->post_modified_gmt);
			if (empty($gUserMap[$post->post_author])) {
				$pParamHash['owner_id'] = 1;
				$gErrorMap[]['warning'] = "Blog Post: " . $pParamHash['title'] . " author defaulted to Administrator.";
			} else {
				$pParamHash['owner_id'] = $gUserMap[$post->post_author];
				$pParamHash['current_owner_id'] = -1;
			}

			/** @TODO : Check attachments **/

			$bp = new BitBlogPost();
			$bp->store($pParamHash);
			if (empty($bp->mErrors)) {
				$gPostMap[$post->ID] = $bp->mContentId;
				$query = "UPDATE liberty_content SET created = ? WHERE content_id = ?";
				$gBitSystem->mDb->query($query, array($pParamHash['publish_date'], $bp->mContentId));
			} else {
				$pErrorMap[]['error'] = "Blog Post: " . $pParamHash['title'] . " had errors " . implode(", ", $bp->mErrors);
			}
		}
	}

	$sql = "INSERT INTO `".BIT_DB_PREFIX."blogs_wp_posts` (`wp_id`, `post_id`) VALUES (?, ?)";
	foreach($gPostMap as $wp_id => $post_id) {
		if ($wp_id > $gMaxPost) {
			$gBitSystem->mDb->query($sql, array($wp_id, $post_id));
		}
	}
}

function migrate_wp_post_map() {
	global $wpdb, $gBitSystem, $gBitSmarty, $gUserMap, $gErrorMap, $gBlogMap, $gPostMap;

	$query = "select * from ".$wpdb->table_prefix."wp_post2cat";

	$post2cat = $wpdb->get_results($query);

	if (!empty($post2cat)) {
		foreach ($post2cat as $map) {
			$post = new BitBlogPost();
			if (!empty($gPostMap[$map->post_id])) {

				if (!empty($gBlogMap[$map->category_id])) {
					$post->storePostMap($gPostMap[$map->post_id], $gBlogMap[$map->category_id]);
				}
				else {
					$gErrorMap[]['error'] = "Post2Category: ".$map->post_id." Unable to map category: ".$map->category_id;
				}
			}
			else {
				$gErrorMap[]['error'] = "Post2Category: Unable to map post_id: " . $map->post_id." category: ".$map->category_id;
			}
		}
	}
}

function migrate_wp_comments() {
	global $wpdb, $gBitSystem, $gBitSmarty, $gUserMap, $gErrorMap, $gBlogMap, $gPostMap, $gCommentMap, $gMaxComment;

	//  vd("Blog Map");
	//  vd($gBlogMap);
	//  vd("Post Map");
	//  vd($gPostMap);
	//  vd("User map");
	//  vd($gUserMap);

	$query = "select * from ".$wpdb->table_prefix."wp_comments WHERE comment_type = '' AND comment_id > $gMaxComment ORDER BY comment_id";

	$comments = $wpdb->get_results($query);

	if (!empty($comments)) {
		foreach ($comments as $comment) {
			//    vd($comment);
			$pParamHash = array();
			$pParamHash['edit'] = wptexturize(convert_chars(wpautop($comment->comment_content)));
			if (empty($comment->user_id)) {
				$pParamHash['annon_name'] = $comment->comment_author;
			} else {
				if (empty($gUserMap[$comment->user_id])) {
					$pParamHash['owner_id'] = 1;
					$gErrorMap[]['warning'] = "Comment: " . $comment->comment_ID . " author defaulted to Administrator.";
				} else {
					$pParamHash['owner_id'] = $gUserMap[$comment->user_id];
					$pParamHash['current_owner_id'] = -1;
				}
			}
			if ($comment->comment_approved) {
				$pParamHash['content_status'] = 50;
			} else {
				$pParamHash['content_status'] = -1;
			}
			$pParamHash['last_modified'] = $gBitSystem->mServerTimestamp->getTimestampFromIso($comment->comment_date_gmt);
			if (!empty($gPostMap[$comment->comment_post_ID])) {
				$pParamHash['root_id'] = $gPostMap[$comment->comment_post_ID];
				$pParamHash['parent_id'] = $gPostMap[$comment->comment_post_ID];
			} else {
				$gErrorMap[]['error'] = "Comment: Unable to map to post_id: " . $comment->comment_ID . " : Post ID: " . $comment->comment_post_ID;
			}
			$c = new LibertyComment();
			$c->storeComment($pParamHash);
			if (!empty($c->mContentId)) {
				$gCommentMap[$comment->comment_ID] = $c->mContentId;
				$query = "UPDATE liberty_content set IP = ?, created = ? WHERE content_id = ?";
				$gBitSystem->mDb->query($query, array($comment->comment_author_IP, $pParamHash['last_modified'], $c->mContentId));
			} else {
				$gErrorMap[]['error'] = "Coment: Unable to store: " . $comment->comment_ID . " : " . implode(", ", $c->mErrors);
			}
		}
	}
	//  vd("Comments Map.");
	//  vd($gCommentMap);
	$sql = "INSERT INTO `".BIT_DB_PREFIX."blogs_wp_comments` (`wp_id`, `comment_id`) VALUES (?, ?)";
	foreach($gCommentMap as $wp_id => $comment_id) {
		if ($wp_id > $gMaxComment) {
			$gBitSystem->mDb->query($sql, array($wp_id, $comment_id));
		}
	}
}

?>
