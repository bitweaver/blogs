<?php
/**
 * @version $Header$
 * @version  $Revision$
 * @package blogs
 */

/**
 * required setup
 */
require_once( BLOGS_PKG_PATH.'BitBlogPost.php');
require_once( LIBERTY_PKG_PATH.'LibertyComment.php');

define( 'BITBLOG_CONTENT_TYPE_GUID', 'bitblog' );

/**
 * @package blogs
 */
class BitBlog extends LibertyMime {
	var $mBlogId;

	function BitBlog( $pBlogId=NULL, $pContentId=NULL ) {
		$this->mBlogId = @$this->verifyId( $pBlogId ) ? $pBlogId : NULL;
		parent::__construct( $pContentId );
		$this->registerContentType( BITBLOG_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITBLOG_CONTENT_TYPE_GUID,
			'content_name' => 'Blog',
			'handler_class' => 'BitBlog',
			'handler_package' => 'blogs',
			'handler_file' => 'BitBlog.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITBLOG_CONTENT_TYPE_GUID;

		// Permission setup
		$this->mViewContentPerm  = 'p_blogs_view';
		$this->mUpdateContentPerm  = 'p_blogs_update';
		$this->mCreateContentPerm  = 'p_blogs_create';
		$this->mAdminContentPerm = 'p_blogs_admin';
	}

	function get_num_user_blogs($user_id) {
		$ret = NULL;
		if ($user_id) {
			$sql = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."blogs` WHERE `user_id` = ?";
			$ret = $this->mDb->getOne($sql, array( $user_id ));
		}
		return $ret;
	}

	public static function getDisplayUrlFromHash( &$pParamHash ) {
		global $gBitSystem;
		$ret = NULL;

		if ( BitBase::verifyId( $pParamHash['blog_id'] ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				$ret = BLOGS_PKG_URL.'view/'.$pParamHash['blog_id'];
			} elseif( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = BLOGS_PKG_URL.$pParamHash['blog_id'];
			} else {
				$ret = BLOGS_PKG_URL.'view.php?blog_id='.$pParamHash['blog_id'];
			}
		} else {
			$ret = parent::getDisplayUrlFromHash( $pParamHash );
		}
		return $ret;
	}

	function getDisplayUrl() {
		$ret = NULL;
		if( $this->isValid() ) {
			$ret = self::getDisplayUrlFromHash( array ( 'blog_id' => $pBlogId ) );
		}
		return $ret;
	}

	/**
	* Check if there is an article loaded
	* @return bool TRUE on success, FALSE on failure
	* @access public
	**/
	function isValid() {
		return( $this->verifyId( $this->mBlogId ) && $this->verifyId( $this->mContentId ) );
	}

	function load( $pContentId = NULL, $pPluginParams = NULL ) {
		$this->mInfo = $this->getBlog( $this->mBlogId, $this->mContentId );
		$this->mContentId = $this->getField( 'content_id' );
		$this->mBlogId = $this->getField('blog_id');
	}


	/*shared*/
	function getBlog( $pBlogId, $pContentId = NULL ) {
		global $gBitSystem;
		$ret = NULL;

		$lookupId = (!empty( $pBlogId ) ? $pBlogId : $pContentId);
		$lookupColumn = (!empty( $pBlogId ) ? 'blog_id' : 'content_id');

		$bindVars = array( (int)$lookupId );
		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		if ( BitBase::verifyId( $lookupId ) ) {
			$query = "
				SELECT b.*, lc.*, lch.`hits`, uu.`login`, uu.`login`, uu.`user_id`, uu.`real_name`,
					lfa.`file_name` as `avatar_file_name`, lfa.`mime_type` AS `avatar_mime_type`, laa.`attachment_id` AS `avatar_attachment_id`,
					lfp.`file_name` AS `image_file_name`, lfp.`mime_type` AS `image_mime_type`, lap.`attachment_id` AS `image_attachment_id`
					$selectSql
				FROM `".BIT_DB_PREFIX."blogs` b
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
					$joinSql
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON (lc.`content_id` = lch.`content_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`	laa ON (uu.`user_id` = laa.`user_id` AND laa.`attachment_id` = uu.`avatar_attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`	    lfa ON lfa.`file_id`		   = laa.`foreign_id`
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`  lap ON lap.`content_id`        = lc.`content_id` AND lap.`is_primary` = 'y'
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`        lfp ON lfp.`file_id`           = lap.`foreign_id`
				WHERE b.`$lookupColumn`= ? $whereSql";

			if( $this->mInfo = $this->mDb->getRow($query,$bindVars) ) {
				$this->mContentId = $this->getField( 'content_id' );
				$this->mBlogId = $this->getField('blog_id');
				foreach( array( 'avatar', 'image' ) as $img ) {
					$this->mInfo[$img] = liberty_fetch_thumbnails( array(
						'source_file' => $this->getSourceFile( array( 'user_id'=>$this->getField( 'user_id' ), 'package'=>liberty_mime_get_storage_sub_dir_name( array( 'type' => $this->getField( $img.'_mime_type' ), 'name' =>  $this->getField( $img.'_file_name' ) ) ), 'file_name' => basename( $this->mInfo[$img.'_file_name'] ), 'sub_dir' =>  $this->getField( $img.'_attachment_id' ) ) )
					));
				}
				parent::load();
				$this->mInfo['postscant'] = $this->getPostsCount( $this->mContentId );
			}
		}
		return count( $this->mInfo ) != 0;
	}

	function verify( &$pParamHash ) {
		global $gBitUser;

		$pParamHash['blog_store']['max_posts'] = !empty( $pParamHash['max_posts'] ) && is_numeric( $pParamHash['max_posts'] ) ? $pParamHash['max_posts'] : NULL;
		$pParamHash['blog_store']['use_title'] = isset( $pParamHash['use_title'] ) ? 'y' : 'n';
		$pParamHash['blog_store']['allow_comments'] = isset( $pParamHash['allow_comments'] ) ? 'y' : 'n';
		$pParamHash['blog_store']['use_find'] = isset( $pParamHash['use_find'] ) ? 'y' : 'n';

		// if we have an error we get them all by checking parent classes for additional errors
		if( count( $this->mErrors ) > 0 ){
			parent::verify( $pParamHash );
		}

		return( count( $this->mErrors ) == 0 );
	}

	function store( &$pParamHash ) {
		global $gBitSystem;
		$this->mDb->StartTrans();
		if( $this->verify( $pParamHash ) && parent::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."blogs";
			if( $this->isValid() ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['blog_store'], array( "blog_id" => $pParamHash['blog_id'] ) );
			} else {
				// DEPRECATED - this looks stupid -wjames5
				//$pParamHash['blog_store']['posts'] = 0;
				$pParamHash['blog_store']['content_id'] = $this->mContentId;
				if( isset( $pParamHash['blog_id'] )&& is_numeric( $pParamHash['blog_id'] ) ) {
					// if pParamHash['blog_id'] is set, someone is requesting a particular blog_id. Use with caution!
					$pParamHash['blog_store']['blog_id'] = $pParamHash['blog_id'];
				} else {
					$pParamHash['blog_store']['blog_id'] = $this->mDb->GenID( 'blogs_blog_id_seq' );
				}
				$this->mBlogId = $pParamHash['blog_store']['blog_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['blog_store'] );
			}
			$this->mDb->CompleteTrans();
		}
		return( count( $this->mErrors ) == 0 );
	}

	function expunge() {
		$ret = FALSE;
		if ( $this->isValid() ) {
			$this->mDb->StartTrans();

			// remove all references in blogs_posts_map where post_content_id = content_id
			$query_map = "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `blog_content_id` = ?";
			$result = $this->mDb->query( $query_map, array( $this->mContentId ) );

			$query = "DELETE from `".BIT_DB_PREFIX."blogs` where `content_id`=?";
			$result = $this->mDb->query( $query, array( (int)$this->mContentId ) );

			if( parent::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
			$this->mDb->CompleteTrans();
		}
		return $ret;
	}

	function getPost( $pListHash=array() ) {
		$ret = NULL;
		$bindVars = array();

		$blogId = (!empty( $pListHash['blog_id'] ) ? $pListHash['blog_id'] : $this->mBlogId);

		if ( BitBase::verifyId( $blogId ) ) {
			$this->prepGetList( $pListHash );
			$sql = "SELECT bp.`post_id`
					FROM `".BIT_DB_PREFIX."blog_posts` bp
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id`=bp.`content_id`)
						INNER JOIN `".BIT_DB_PREFIX."blogs_posts_map` bpm ON (bp.`content_id`=bpm.`post_content_id`)
						INNER JOIN `".BIT_DB_PREFIX."blogs` b on (bpm.`blog_content_id`=b.`content_id`)
					WHERE b.`blog_id` = ? ORDER BY ".$this->mDb->convertSortMode( $pListHash['sort_mode'] );
			if( $postId = $this->mDb->getOne($sql, array( $blogId ) ) ) {
				$blogPost = new BitBlogPost( $postId );
				$blogPost->load( NULL, $pListHash );
				$ret = $blogPost;
			}
		}
		return $ret;
	}

	// BLOG METHODS ////
	function getList( &$pParamHash ) {
		global $gBitSystem;

		LibertyContent::prepGetList( $pParamHash );

		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$bindVars = array();
//		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		// You can use a title or an array of blog_id
		if (!empty($pParamHash['find'])) {
			if (is_array($pParamHash['find'])) {
				$whereSql .= " AND b.`blog_id` IN ( ".implode( ',',array_fill( 0,count( $pParamHash['find'] ),'?' ) ).") ";
				$bindVars = array_merge($bindVars, $pParamHash['find']);
			}
			else {
				$findesc = '%' . strtoupper( $pParamHash['find'] ) . '%';
				$whereSql = " AND (UPPER(lc.`title`) like ? or UPPER(lc.`data`) like ?) ";
				$bindVars=array($findesc,$findesc);
			}
		}
		if( @$this->verifyId( $pParamHash['user_id'] ) ) {
			$whereSql .= " AND uu.`user_id` = ? ";
			$bindVars[] = $pParamHash['user_id'];
		}

		$this->getServicesSql( 'content_user_collection_function', $selectSql, $joinSql, $whereSql, $bindVars, $this, $pListHash );

		if( !empty( $pParamHash['is_active'] ) ) {
			$whereSql .= " AND b.`activity` IS NOT NULL";
		}

		if( !empty( $pParamHash['is_hit'] ) ) {
			$whereSql .= " AND lch.`hits` IS NOT NULL";
		}

		if( !empty( $pParamHash['content_perm_name'] ) ) {
			$this->getContentPermissionsSql( $pParamHash['content_perm_name'], $selectSql, $joinSql, $whereSql, $bindVars );
		}

		if( !empty( $whereSql ) ) {
			$whereSql = preg_replace( '/^[\s]*AND/', ' WHERE ', $whereSql );
		}


		$ret = array();

		// Return a data array, even if empty
		$pParamHash["data"] = array();

		# Get count of total number of items available
		$query_cant = "
			SELECT COUNT(b.`blog_id`)
				FROM `".BIT_DB_PREFIX."blogs` b
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
				$joinSql
			$whereSql";
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		# Check for offset out of range
		if ( $pParamHash['offset'] < 0 ) {
			$pParamHash['offset'] = 0;
			}
		elseif ( $pParamHash['offset']	> $pParamHash["cant"] ) {
			$lastPageNumber = ceil ( $pParamHash["cant"] / $pParamHash['max_records'] ) - 1;
			$pParamHash['offset'] = $pParamHash['max_records'] * $lastPageNumber;
			}

		$query = "
			SELECT b.`content_id` AS `hash_key`,
				b.`blog_id`, b.`is_public`, b.`max_posts`, b.`activity`, b.`use_find`, b.`use_title`,
				b.`add_date`, b.`add_poster`, b.`allow_comments`,
				uu.`login`,	uu.`real_name`, lc.*, lch.hits $selectSql
			FROM `".BIT_DB_PREFIX."blogs` b
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = b.`content_id`)
				INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id` = lc.`user_id`)
				$joinSql
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON (lc.`content_id` = lch.`content_id`)
			$whereSql order by ".$this->mDb->convertSortmode($pParamHash['sort_mode']);

		$result = $this->mDb->query( $query, $bindVars, $pParamHash['max_records'], $pParamHash['offset'] );
		$ret = array ();
		while ($res = $result->fetchRow()) {
			$blogContentId = $res['content_id'];
			$ret[$blogContentId] = $res;
			$ret[$blogContentId]['blog_url'] = $this->getDisplayUrl( $res['blog_id'] );
			//get count of post in each blog
			$ret[$blogContentId]['postscant'] = $this->getPostsCount( $res['content_id'] );
			// deal with the parsing
			$parseHash['format_guid']   = $res['format_guid'];
			$parseHash['content_id']    = $res['content_id'];
			$parseHash['data'] 	= $res['data'];
			$ret[$blogContentId]['parsed'] = $this->parseData( $parseHash );
		}

		LibertyContent::postGetList( $pParamHash );

		return $ret;
	}

	function getPostsCount($pBlogContentId){
		global $gBitSystem;
		$ret = NULL;
		if( @$this->verifyId( $pBlogContentId ) ) {
			$whereSql = 'bpm.`blog_content_id` = ?';
			$bindVars = array((int)$pBlogContentId);
			BitBlogPost::getDateRestrictions(array(), $whereSql, $bindVars);
			$query = "SELECT COUNT(*)
				FROM `".BIT_DB_PREFIX."blogs_posts_map` bpm
				INNER JOIN `".BIT_DB_PREFIX."blog_posts` bp ON (bpm.`post_content_id`=bp.`content_id`)
				WHERE $whereSql";

			$ret = $this->mDb->getOne( $query, $bindVars );
		} else {
			$this->mErrors['content_id'] = "Invalid blog content id.";
		}
		return $ret;
	}

	//This doesnt even appear to be used in blogs before this refactoring -wjames5
	function viewerCanPostIntoBlog() {
		global $gBitUser;
		return ($this->getField('user_id') == $gBitUser->mUserId || $gBitUser->isAdmin() || $this->getField('is_public') == 'y' );
	}

	function hasPostPermission() {
		$ret = FALSE;
		if( $this->isValid() ) {
			// for now just check edit permission, however eventually we'll want to separate this notion so blog editors and posters can be distinguished
			$ret = $this->hasUpdatePermission();
		}
		return $ret;
	}

	function viewerHasPermission($pPermName = NULL) {
		global $gBitUser;
		$ret = FALSE;
		if ($gBitUser->mUserId && $pPermName) {
			$ret = $gBitUser->object_has_permission( $gBitUser->mUserId, $this->mInfo['blog_id'], $this->getContentType(), $pPermName );
		}
		return $ret;
	}

	function getViewTemplate( $pAction ){
		$ret = null;
		switch ( $pAction ){
			case "view":
				$ret = "bitpackage:blogs/center_".$pAction."_blog_posts.tpl";
				break;
			case "list":
				$ret = "bitpackage:liberty/center_".$pAction."_generic.tpl";
				break;
		}
		return $ret;
	}

	/**
	 * getContentStatus
	 *
	 * @access public
	 * @return an array of content_status_id, content_status_names the current
	 * user can use on this content.
	 */
	function getAvailableContentStatuses( $pUserMinimum=-100, $pUserMaximum=100 ) {
		global $gBitUser;
		$ret = NULL;
	 	// return NULL for all but admins
		if( $gBitUser->hasPermission( 'p_liberty_edit_all_status' )) {
			$ret = LibertyMime::getAvailableContentStatuses();
		}
		return $ret;
	}
}

function blogs_module_display(&$pParamHash){
	global $gBitThemes, $gBitSmarty, $gBitSystem;
	if( $gBitThemes->isModuleLoaded( 'bitpackage:blogs/center_list_blog_posts.tpl', 'c' ) && $gBitSystem->isFeatureActive( 'blog_ajax_more' ) && $gBitThemes->isJavascriptEnabled() ) {
		$gBitSmarty->assign( 'ajax_more', TRUE );
		$gBitThemes->loadAjax( 'mochikit', array( 'Iter.js', 'DOM.js', 'Style.js', 'Color.js', 'Position.js', 'Visual.js' ));
	}
}
?>
