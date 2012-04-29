<?php
/**
 * $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision$
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyComment.php');
require_once( LIBERTY_PKG_PATH.'LibertyMime.php');
require_once( BLOGS_PKG_PATH.'BitBlog.php');

define( 'BITBLOGPOST_CONTENT_TYPE_GUID', 'bitblogpost' );

/**
 * @package blogs
 */
class BitBlogPost extends LibertyMime {
	var $mPostId;

	function BitBlogPost( $pPostId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->registerContentType( BITBLOGPOST_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITBLOGPOST_CONTENT_TYPE_GUID,
			'content_name' => 'Blog Post',
			'handler_class' => 'BitBlogPost',
			'handler_package' => 'blogs',
			'handler_file' => 'BitBlogPost.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mPostId = (int)$pPostId;
		$this->mContentId = (int)$pContentId;
		$this->mContentTypeGuid = BITBLOGPOST_CONTENT_TYPE_GUID;

		// Permission setup
		$this->mViewContentPerm  = 'p_blogs_view';
		$this->mCreateContentPerm  = 'p_blogs_post';
		$this->mUpdateContentPerm  = 'p_blogs_update';
		$this->mAdminContentPerm = 'p_blogs_admin';
	}

	/**
	 * Load a Blog Post section
	 */
	function load( $pContentId = NULL, $pPluginParams = NULL ) {
		if( $this->verifyId( $this->mPostId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem, $gBitUser, $gLibertySystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
			$lookupColumn = $this->verifyId( $this->mPostId )? 'post_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mPostId )? $this->mPostId : $this->mContentId;
			array_push( $bindVars, $lookupId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT bp.*, lc.*, lcds.`data` AS `summary`, lch.`hits`, uu.`login`, uu.`real_name`, 
					lfa.`file_name` as `avatar_file_name`, lfa.`mime_type` AS `avatar_mime_type`, laa.`attachment_id` AS `avatar_attachment_id`,
					lfp.`file_name` AS `image_file_name`, lfp.`mime_type` AS `image_mime_type`, lap.`attachment_id` AS `image_attachment_id`
					$selectSql
				FROM `".BIT_DB_PREFIX."blog_posts` bp
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = bp.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON( uu.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_data` lcds ON (lc.`content_id` = lcds.`content_id` AND lcds.`data_type`='summary')
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` laa ON (uu.`user_id` = laa.`user_id` AND uu.`avatar_attachment_id`=laa.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfa ON (lfa.`file_id` = laa.`foreign_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` lap ON( lap.`content_id` = lc.`content_id` AND lap.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = lap.`foreign_id` )
					$joinSql
				WHERE bp.`$lookupColumn`=? $whereSql ";

			if( $this->mInfo = $this->mDb->getRow( $query, $bindVars ) ) {
				$this->mPostId = $this->mInfo['post_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mInfo['blogs'] = $this->getBlogMemberships( $this->mContentId );
				// this is bad news right here, 'url' is wrong, standard is 'display_url'
				// we should remove this now that display_url is added
				$this->mInfo['url'] = BitBlogPost::getDisplayUrlFromHash( $this->mContentId, $this->mInfo );
				$this->mInfo['display_url'] = BitBlogPost::getDisplayUrlFromHash( $this->mContentId, $this->mInfo );
				foreach( array( 'avatar', 'image' ) as $img ) {
					$this->mInfo[$img] = liberty_fetch_thumbnails( array(
						'source_file' => $this->getSourceFile( array( 'user_id'=>$this->getField( 'user_id' ), 'package'=>liberty_mime_get_storage_sub_dir_name( array( 'type' => $this->getField( $img.'_mime_type' ), 'name' =>  $this->getField( $img.'_file_name' ) ) ), 'file_name' => basename( $this->mInfo[$img.'_file_name'] ), 'sub_dir' =>  $this->getField( $img.'_attachment_id' ) ) )
					));
				}

				$this->mInfo['raw'] = $this->mInfo['data'];
				//for two text field auto split
				if( $gBitSystem->isFeatureActive( 'blog_posts_autosplit' ) && preg_match( LIBERTY_SPLIT_REGEX, $this->mInfo['raw'] )){
					$format = $this->mInfo['format_guid'];
					$linebreak = $gLibertySystem->mPlugins[$format]['linebreak'];
					if ( preg_match( "/\.{3}split\.{3}(".preg_quote( $linebreak, "/" )."){2}/i", $this->mInfo['raw'] ) ){
						$parts = preg_split( "/\.{3}split\.{3}(".preg_quote( $linebreak, "/" )."){2}/i", $this->mInfo['raw'] );
					}else{
						$parts = preg_split( "/\.{3}split\.{3}/i", $this->mInfo['raw'] );
					}
					$this->mInfo['raw'] = isset( $parts[0] )? $parts[0] : $this->mInfo['raw'];
					$this->mInfo['raw_more'] = isset( $parts[1] )? $parts[1] : NULL ;
				}

				$this->mInfo['data'] = preg_replace( LIBERTY_SPLIT_REGEX, "", $this->mInfo['data'] );
				$this->mInfo['use_title'] = $gBitUser->getPreference( 'user_blog_posts_use_title', 'y', $this->mInfo['user_id'] ) ;

				if( isset($pPluginParams['load_comments']) and $pPluginParams['load_comments'] ) {
					$comment = new LibertyComment();
					$comment->mRootObj = $this;
					$this->mInfo['num_comments'] = $comment->getNumComments($this->mInfo['content_id']);
					// Get the comments associated with this post
					$this->mInfo['comments'] = $comment->getComments($this->mInfo['content_id'], $gBitSystem->getConfig( 'comments_per_page', 10 ) );
				}

				if (!$this->mInfo['trackbacks_from'] || $this->mInfo['trackbacks_from']===null)
					$this->mInfo['trackbacks_from'] = serialize(array());

				if (!$this->mInfo['trackbacks_to'] || $this->mInfo['trackbacks_to']===null)
					$this->mInfo['trackbacks_to'] = serialize(array());

				$this->mInfo['trackbacks_from_count'] = count(array_keys(unserialize($this->mInfo['trackbacks_from'])));
				$this->mInfo['trackbacks_from'] = unserialize($this->mInfo['trackbacks_from']);
				$this->mInfo['trackbacks_to'] = unserialize($this->mInfo['trackbacks_to']);
				$this->mInfo['trackbacks_to_count'] = count($this->mInfo['trackbacks_to']);

				LibertyMime::load();
				if( $this->mStorage ) {
					foreach( array_keys( $this->mStorage ) as $key ) {
						$this->mStorage[$key]['wiki_plugin_link'] = '{attachment id='.$key.'}';
					}
				}
			} else {
				$this->mPostId = NULL;
				$this->mContentId = NULL;
			}
		}
		return( count( $this->mInfo ) );
	}

	function getTitle() {
		$ret = NULL;
		if( $this->isValid() ) {
			$ret = self::getTitleFromHash( $this->mInfo );
		}
		return $ret;
	}

	public static function getTitleFromHash( $pHash, $pDefault=TRUE ) {
		global $gBitSystem;
		$ret = NULL;
		if( !empty( $pHash['title'] ) ) {
			$ret = $pHash['title'];
		} elseif( !is_null( $pHash ) ) {
			$date_format = $gBitSystem->get_long_date_format();
			if( $gBitSystem->get_display_offset() ) {
				$date_format = preg_replace( "/ ?%Z/", "", $date_format );
			} else {
				$date_format = preg_replace( "/%Z/", "UTC", $date_format );
			}
			$date_string = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC( !empty($pHash['created']) ? $pHash['created'] : $gBitSystem->getUTCTime());
			$ret = $gBitSystem->mServerTimestamp->strftime( $date_format, $date_string, true );
		}

		return $ret;
	}

	function getBlogMemberships( $pPostContentId ){
		global $gBitSystem;
		$ret = NULL;
		if( @$this->verifyId( $pPostContentId ) ) {
			$bindVars = array( (int)$pPostContentId );
			$query = "SELECT b.`content_id` AS hash_key, bpm.*, b.*, lc.*
				FROM `".BIT_DB_PREFIX."blogs_posts_map` bpm
				INNER JOIN		`".BIT_DB_PREFIX."blogs`				 b ON b.`content_id` = bpm.`blog_content_id`
				INNER JOIN		`".BIT_DB_PREFIX."liberty_content`		lc ON lc.`content_id` = b.`content_id`
				WHERE bpm.post_content_id = ?";

			if( $ret = $this->mDb->getAssoc( $query, $bindVars ) ) {
				foreach( array_keys( $ret ) as $blogContentId ) {
					$ret[$blogContentId]['blog_url'] = BitBlog::getDisplayUrlFromHash( $ret[$blogContentId]['blog_id'] );
				}
			}
		} else {
			$this->mErrors['post_id'] = "Invalid post id.";
		}
		return $ret;
	}

	/**
	* Get the URL for any given post image
	* @param $pParamHash pass in full set of data returned from post query
	* @return url to image
	* @access public
	**/
	function getImageThumbnails( $pParamHash ) {
		global $gBitSystem, $gThumbSizes;
		$ret = NULL;
		if( !empty( $pParamHash['image_file_name'] )) {
			$thumbHash = array(
				'mime_image'   => FALSE,
				'source_file' => $pParamHash['image_file_name']
			);
			$ret = liberty_fetch_thumbnails( $thumbHash );
			$ret['original'] = BIT_ROOT_URL.$pParamHash['image_file_name'];
		}
		return $ret;
	}

	/**
	* Deal with images and text, modify them apprpriately that they can be returned to the form.
	* @param $previewData data submitted by form - generally $_REQUEST
	* @return array of data compatible with article form
	* @access public
	**/
	function preparePreview( $pParamHash ) {
		global $gBitSystem, $gBitUser;

		$data = $pParamHash;
		// preserve our split data if we are using to text fields cause it gets merged in verify
		$data['raw'] = $data['edit'];
		$data['raw_more'] = (!empty($data['edit_body'])?$data['edit_body']:'');
		$this->verify( $data );

		if( empty( $data['user_id'] ) ) {
			$data['user_id'] = $gBitUser->mUserId;
		}

		if( empty( $data['hits'] ) ) {
			$data['hits'] = 0;
		}

		if( empty( $data['publish_date'] ) ) {
			$data['publish_date'] = $gBitSystem->getUTCTime();
		}

		// preserve checked blogs
		if( !empty($pParamHash['blog_content_id']) ){
			foreach($pParamHash['blog_content_id'] as $blog_content_id) {
				$this->mInfo['blogs'][$blog_content_id] = $blog_content_id;
			}
		}

		$data['use_title'] = $gBitUser->getPreference( 'user_blog_posts_use_title', 'y', $data['user_id'] );
		$data['title'] = $this->getTitle($pParamHash);

		if( empty( $data['parsed_data'] ) ) {
			$data['no_cache']    = TRUE;
			/* this is already taken care of by calling verify above
			if (isset($data['edit_body'])){
				$data['edit'] .= "...split...".$data['edit_body'];
			}
			*/
			$data['parsed_data'] = $this->parseData( $data['edit'], (!empty($data['format_guid']) ? $data['format_guid'] : 'tikiwiki' ));
			//$data['parsed_data'] = $this->parseData( $data );
			// replace the split syntax with a horizontal rule
			$data['parsed_data'] = preg_replace( LIBERTY_SPLIT_REGEX, "<hr />", $data['parsed_data'] );
		}

		return $data;
	}



	/**
	* Make sure the data is safe to store
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	* This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	* NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	*
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	*
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	*
	* @access private
	**/
	function verify( &$pParamHash ) {
		global $gBitUser, $gBitSystem, $gLibertySystem;

		// make sure we're all loaded up of we have a mPostId
		if( $this->verifyId( $this->mPostId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( empty( $pParamHash['content_type_guid'] )&& !empty( $this->mContentTypeGuid ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( !empty( $pParamHash['data'] ) ) {
			$pParamHash['edit'] = $pParamHash['data'];
		}

		// for two text field auto split
		if (!empty($pParamHash['edit_body'])){
			$linebreak = $gLibertySystem->mPlugins[$pParamHash['format_guid']]['linebreak'];
			// we need two line breaks to simulate a paragraph break
			$pParamHash['edit'] .= "...split...".$linebreak.$linebreak.$pParamHash['edit_body'];
		}

		// truncate length if too long
		if( !empty( $pParamHash['title'] ) ) {
			$pParamHash['title'] = substr( $pParamHash['title'], 0, 160 );
		}

		if( !empty( $pParamHash['publish_Month'] ) ) {
			//$dateString = $pParamHash['publish_Year'].'-'.$pParamHash['publish_Month'].'-'.$pParamHash['publish_Day'].' '.$pParamHash['publish_Hour'].':'.$pParamHash['publish_Minute'];

			//old way
			//$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			//new way
			$this->mDate = new BitDate(0);
			$offset = $this->mDate->get_display_offset();

			$dateString = $this->mDate->gmmktime(
				$pParamHash['publish_Hour'],
				$pParamHash['publish_Minute'],
				isset($pParamHash['publish_Second']) ? $pParamHash['publish_Second'] : 0,
				$pParamHash['publish_Month'],
				$pParamHash['publish_Day'],
				$pParamHash['publish_Year']
			);

			$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );

			if( $timestamp !== -1 ) {
				$pParamHash['publish_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['publish_date'] ) ) {
			$pParamHash['post_store']['publish_date'] = $pParamHash['publish_date'];
		}else{
			$pParamHash['post_store']['publish_date'] = $gBitSystem->getUTCTime();
		}

		if( !empty( $pParamHash['expire_Month'] ) ) {
			$dateString = $pParamHash['expire_Year'].'-'.$pParamHash['expire_Month'].'-'.$pParamHash['expire_Day'].' '.$pParamHash['expire_Hour'].':'.$pParamHash['expire_Minute'];

			//old way
			//$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			//new way
			$this->mDate = new BitDate(0);
			$offset = $this->mDate->get_display_offset();

			$dateString = $this->mDate->gmmktime(
				$pParamHash['expire_Hour'],
				$pParamHash['expire_Minute'],
				isset($pParamHash['expire_Second']) ? $pParamHash['expire_Second'] : 0,
				$pParamHash['expire_Month'],
				$pParamHash['expire_Day'],
				$pParamHash['expire_Year']
			);

			$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );

			if( $timestamp !== -1 ) {
				$pParamHash['expire_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['expire_date'] ) ) {
			$pParamHash['post_store']['expire_date'] = $pParamHash['expire_date'];
		}else{
			$pParamHash['post_store']['expire_date'] = $gBitSystem->getUTCTime();
		}

		// if we have an error we get them all by checking parent classes for additional errors
		if( count( $this->mErrors ) > 0 ){
			parent::verify( $pParamHash );
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * Check that the class has a valid blog loaded
	 */
	function isValid() {
		return( $this->verifyId( $this->mPostId ) && is_numeric( $this->mPostId ) && $this->mPostId > 0 );
	}

	/**
	 * Check if the current user is the blog owner
	 */
 	function isBlogOwner( $pUserId=NULL ) {
		$ret = FALSE;
		global $gBitUser;
		if( empty( $pUserId ) && $gBitUser->isValid() ) {
			$pUserId = $gBitUser->mUserId;
		}
		if( $this->isValid() && ($pUserId == $this->mInfo["blog_user_id"]) ) {
			$ret = 'y';
		}
		return $ret;
	}


	/**
	 * Check if the current post can have comments attached to it
	 */
	function isCommentable(){
		global $gBitSystem;
		return $gBitSystem->isFeatureActive( 'blog_posts_comments' );
	}

	/**
	 * Store a Blog Post
	 * @todo users_watches is a legacy package and needs refactoring
	 */
	function store( &$pParamHash ) {
		global $gBitSystem;
		$this->mDb->StartTrans();
		if( $this->verify( $pParamHash )&& LibertyMime::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."blog_posts";

			// Send trackbacks recovering only successful trackbacks
			if ( !empty( $pParamHash['trackback'] ) ){
				$trackbacks = serialize( $this->sendTrackbacks( $pParamHash['trackback'] ) );
			}

			if( $this->isValid() ) {
				$locId = array( "content_id" => $this->mContentId );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['post_store'], $locId );
			} else {
				$pParamHash['post_store']['content_id'] = $pParamHash['content_id'];
				if( @$this->verifyId( $pParamHash['post_id'] ) ) {
					// if pParamHash['post_id'] is set, someone is requesting a particular post_id. Use with caution!
					$pParamHash['post_store']['post_id'] = $pParamHash['post_id'];
				} else {
					$pParamHash['post_store']['post_id'] = $this->mDb->GenID( 'blog_posts_post_id_seq' );
				}
				$this->mPostId = $pParamHash['post_store']['post_id'];
				//store the new post
				$result = $this->mDb->associateInsert( $table, $pParamHash['post_store'] );
			}

			// let's reload to get a full mInfo hash which is needed below
			$this->load();

			// if blog_content_id, then map the post to the relative blogs
			if( !empty( $pParamHash['blog_content_id'] )){
				$this->storePostMap( $this->mInfo, $pParamHash['blog_content_id'], NULL, TRUE );
			}

			// Update post with trackbacks successfully sent
			// Can this be moved below into similar function below? -wjames5
			// this throws an error on site population because post_id is not defined in pParamHash - wjames5
			$query = "UPDATE `".BIT_DB_PREFIX."blog_posts` SET `trackbacks_from`=?, `trackbacks_to` = ? WHERE `post_id`=?";
			if( @BitBase::verifyId( $pParamHash['post_id'] )) {
				$this->mDb->query( $query, array( serialize( array() ), $trackbacks, (int) $pParamHash['post_id'] ));
			}

			if( $gBitSystem->isFeatureActive( 'users_watches' ) ) {
				global $gBitUser, $gBitSmarty;
				if( isset( $this->mInfo['blog_id'] ) &&  $nots = $gBitUser->getEventWatches( 'blog_post', $this->mInfo['blog_id'] ) ) {
					foreach ($nots as $not) {
						$gBitSmarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
						$gBitSmarty->assign('mail_title', $this->mInfo['title']);
						$gBitSmarty->assign('mail_blogid', $this->mInfo['blog_id']);
						$gBitSmarty->assign('mail_postid', $this->mPostId);
						$gBitSmarty->assign('mail_date', $gBitSystem->getUTCTime());
						$gBitSmarty->assign('mail_user', $this->mInfo['login']);
						$gBitSmarty->assign('mail_data', $this->mInfo['data']);
						$gBitSmarty->assign('mail_hash', $not['hash']);
						$foo = parse_url($_SERVER["REQUEST_URI"]);
						$machine = httpPrefix(). $foo["path"];
						$gBitSmarty->assign('mail_machine', $machine);
						$parts = explode('/', $foo['path']);

						if (count($parts) > 1)
							unset ($parts[count($parts) - 1]);

						$gBitSmarty->assign('mail_machine_raw', httpPrefix(). implode('/', $parts));
						$mail_data = $gBitSmarty->fetch('bitpackage:blogs/user_watch_blog_post.tpl');
						@mail($not['email'], tra('Blog post'). ' ' . $title, $mail_data, "From: ".$gBitSystem->getPrefence( 'site_sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n");
					}
				}
			}

			//is this nearly identical to the above and can they be consolodated? -wjames5
			// should this be $pParamHash['trackback'] or the above $pParamHash['trackbacks'] ? - xing
			if( !empty( $pParamHash['trackbacks'] ) ) {
				$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_to`=? where `post_id`=?";
				$result = $this->mDb->query($query,array($trackbacks, $user_id, $post_id));
			}

			$this->mDb->CompleteTrans();
			$this->load();
		}
		return ( count( $this->mErrors ) == 0 );
	}


	function loadPostMap( $pPostContentId, $pBlogContentId){
		$ret = NULL;
		if( @BitBase::verifyId( $pPostContentId ) ){
			$this->mDb->StartTrans();
			$result = $this->mDb->getRow( "SELECT * FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `post_content_id`=? AND `blog_content_id`=?", array( $pPostContentId, $pBlogContentId ) );
			$this->mDb->CompleteTrans();
			if ( !empty( $result ) ){
				$ret = $result;
			};
		}
		return $ret;
	}

	/**
	 * Map a Post to a Blog or multiple Blogs
	 * @param pPost a Post hash.
	 * @param pBlogMixed the content_id or and array of ids of the blogs we want the post to show up in.
	 * @param pCrosspostNote text to display with the blog post when viewed in the blog crossposted to.
	 * @param pAutoProcess a bool to distinguish if we are storing from the crosspost interface or from the blog posting interface.
	 */
	function storePostMap( $pPost, $pBlogMixed, $pCrosspostNote = NULL, $pAutoProcess = FALSE ) {
		global $gBitSystem, $gBitUser;
		$postContentId = $pPost['content_id'];
		if( @$this->verifyId( $postContentId ) ) {
			$this->mDb->StartTrans();
			//this is to set the time we add a post to a blog.
			$currTime = $gBitSystem->getUTCTime();
			$postTime = $pPost['publish_date'];
			$timeStamp = ( $postTime > $currTime )?$postTime : $currTime;
			$blogIds = array();

			if( !empty( $pBlogMixed )){
				if (!is_array( $pBlogMixed ) && !is_numeric( $pBlogMixed ) ){
					$blogIds = explode( ",", $pBlogMixed );
				}elseif ( is_array( $pBlogMixed ) ) {
					$blogIds = $pBlogMixed;
				}elseif ( is_numeric( $pBlogMixed ) ) {
					$blogIds = array( $pBlogMixed );
				}
			}
			$currentMappings = array();
			if( $allMappings = $this->mDb->getCol( "SELECT `blog_content_id` FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `post_content_id`=?", array( $postContentId ) ) ) {
				// whiddle down all mappings to just those we have perm to
				foreach( $allMappings as $blogContentId ) {
					if( $this->checkContentPermission( array( 'user_id' => $gBitUser->mUserId, 'perm_name'=>'p_blogs_post', 'content_id'=>$blogContentId ) ) ) {
						$currentMappings[] = $blogContentId;
					}
				}
			}

			// Add new mappings for this post
			$newBlogIds = array_diff( $blogIds, $currentMappings );
			foreach( $newBlogIds as $blogContentId ) {
				if( $this->verifyId( $blogContentId ) && $this->checkContentPermission( array( 'user_id' => $gBitUser->mUserId, 'perm_name'=>'p_blogs_post', 'content_id'=>$blogContentId ) ) ) {
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."blogs_posts_map", array(
						'post_content_id' => $postContentId,
						'blog_content_id' => (int)$blogContentId,
						'date_added' => $timeStamp,
						'crosspost_note' => $pCrosspostNote,
					));
				}
			}

			/* if we are coming form the crossposting form then we
			 * want to update any change to the crosspost note.
			 * we dont want to if we are coming from the blog posting form.
			 */
			if( !$pAutoProcess ){
				// Update existing mappings
				$updateBlogIds = array_intersect( $blogIds, $currentMappings );
				foreach( $updateBlogIds as $blogContentId ) {
					if( $this->verifyId( $blogContentId ) && $this->checkContentPermission( array( 'user_id' => $gBitUser->mUserId, 'perm_name'=>'p_blogs_post', 'content_id'=>$blogContentId ) ) ) {
						$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."blogs_posts_map", array(
							'crosspost_note' => $pCrosspostNote,
						), array(
							'post_content_id' => $postContentId,
							'blog_content_id' => (int)$blogContentId,
						));
					}
				}
			}
			$this->mDb->CompleteTrans();

			/* if we are coming from the blog posting form we
			 * want to automatically drop any crossposting if
			 * we have unchecked them there. we ignore this when
			 * coming from the crossposting form.
			 */
			if ( $pAutoProcess ){
				// Remove mappings for this post
				$removedBlogIds = array_diff( $currentMappings, $blogIds );
				$this->expungePostMap( $postContentId, $removedBlogIds );
			}
		}

		return ( count( $this->mErrors ) == 0 );
	}

	function expungePostMap( $pPostContentId, $pBlogContentIds ){
		$this->mDb->StartTrans();
		if ( !empty($pBlogContentIds) ){
			foreach( $pBlogContentIds as $blogContentId ) {
				$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `blog_content_id`=? AND `post_content_id`=?", array( $blogContentId, $pPostContentId ) );
			}
		}
		$this->mDb->CompleteTrans();
		return ( count( $this->mErrors ) == 0 );
	}

	/**
	 * Remove complete blog post set and any comments
	 */
	function expunge() {
		$ret = FALSE;
		// let's force a full load to make sure everything is loaded.
		// lets not -wjames5
		//$this->load();
		if( $this->isValid() ) {
			$this->mDb->StartTrans();

			// remove all references in blogs_posts_map where post_content_id = content_id
			$query_map = "DELETE FROM `".BIT_DB_PREFIX."blogs_posts_map` WHERE `post_content_id` = ?";
			$result = $this->mDb->query( $query_map, array( $this->mContentId ) );

			$query = "DELETE FROM `".BIT_DB_PREFIX."blog_posts` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );

			// Do this last so foreign keys won't complain (not the we have them... yet ;-)
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	/**
	 * Return a summary for this content base on
	 *
	 * @param	object	PostId of the item to use
	 * @return	object	Url String
	 */
	function getDescription() {
		if( !($ret = $this->getField( 'summary' )) ) {
			$ret = $this->getField( 'data' );
		}
		return $ret;
	}


	/**
	 * Generate a valid url for the Blog
	 *
	 * @param	object	PostId of the item to use
	 * @return	object	Url String
	 */
	public static function getDisplayUrlFromHash( $pContentId = NULL, $pParamHash = NULL ) {
		global $gBitSystem;

		$ret = NULL;
		if( empty( $pContentId ) && $this->isValid() ) {
			$pContentId = $this->mContentId;
			// if we have a post id we'll use it. we should, but nice to check
			if( !empty( $this->mPostId ) ){
				$pParamHash['post_id'] = $this->mPostId;
			}
		}

		if( !@BitBase::verifyId( $pContentId ) && @BitBase::verifyId( $pParamHash['content_id'] )) {
			$pContentId = $pParamHash['content_id'];
		}

		if( @BitBase::verifyId( $pContentId )) {
			$rewrite_tag = $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ? 'view/' : '';
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				if( !empty( $pParamHash['post_id'] ) ) {
					$ret = BLOGS_PKG_URL.$rewrite_tag.'post/'.$pParamHash['post_id'];
				} else {
					$ret = BLOGS_PKG_URL.$rewrite_tag.'content/'.$pContentId;
				}
			} else {
				$ret = BLOGS_PKG_URL.'view_post.php?content_id='.$pContentId;
			}
		}
		return $ret;
	}

	/**
	 * Generate a valid display link for the Blog
	 *
	 * @param	object	PostId of the item to use
	 * @param	array	Not used
	 * @return	object	Fully formatted html link for use by Liberty
	 */
	function getDisplayLink( $pTitle=NULL, $pMixed=NULL, $pAnchor=NULL ) {
		global $gBitSystem;
		if( empty( $pTitle ) && !empty( $this ) ) {
			$pTitle = $this->getField( 'title', $this->getContentTypeName() );
		}

		if( empty( $pMixed ) && !empty( $this ) ) {
			$pMixed = $this->mInfo;
		}

		$ret = $pTitle;
		if( $gBitSystem->isPackageActive( 'blogs' ) ) {
			$ret = '<a title="'.htmlspecialchars( BitBlogPost::getTitle( $pMixed ) ).'" href="'.BitBlogPost::getDisplayUrlFromHash( $pMixed['content_id'] ).'">'.htmlspecialchars( BitBlogPost::getTitle( $pMixed  ) ).'</a>';
		}

		return $ret;
	}

    /**
    * Returns include file that will
    * @return the fully specified path to file to be included
    */
	function getRenderFile() {
		return( BLOGS_PKG_PATH.'display_bitblogpost_inc.php' );
	}

	function sendTrackbacks( $pTrackbacks ) {
		$ret = array();
		if( $this->isValid() && !empty( $pTrackbacks ) ) {
		// Split to get each URI
		$tracks = explode(',', $pTrackbacks);

		//Build uri for post
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$uri = httpPrefix(). str_replace('post',
			'view_post', $parts['path']). '?post_id=' . $this->mPostId . '&amp;blog_id=' . $this->mInfo['blog_id'];
		include_once ( UTIL_PKG_PATH.'Snoopy.class.inc' );
		$snoopy = new Snoopy;

		foreach ($tracks as $track) {
			@$fp = fopen($track, 'r');

			if ($fp) {
				$data = '';

				while (!feof($fp)) {
					$data .= fread($fp, 32767);
				}

				fclose ($fp);
				preg_match("/trackback:ping=(\"|\'|\s*)(.+)(\"|\'\s)/", $data, $reqs);

				if (!isset($reqs[2]))
				return $ret;

				@$fp = fopen($reqs[2], 'r');

					if ($fp) {
						fclose ($fp);

						$submit_url = $reqs[2];
						$submit_vars["url"] = $uri;
						$submit_vars["blog_name"] = $this->mInfo['blogtitle'];
						$submit_vars["title"] = $this->mInfo['title'] ? $this->mInfo['title'] : date("d/m/Y [h:i]", $this->mInfo['created']);
						$submit_vars["title"] .= ' ' . tra('by'). ' ' . BitUser::getDisplayName( FALSE, $this->mInfo );
						$submit_vars["excerpt"] = substr($post_info['data'], 0, 200);
						$snoopy->submit($submit_url, $submit_vars);
						$back = $snoopy->results;

						if (!strstr('<error>1</error>', $back)) {
							$ret[] = $track;
						}
					}
				}
			}
		}
		return $ret;
	}

	function getList( &$pListHash ) {
		global $gBitUser, $gBitSystem;

		$this->prepGetList( $pListHash );

		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );

		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pListHash );

		if( @$this->verifyId( $pListHash['blog_id'] ) ) {
			$selectSql .= ', bpm.crosspost_note';
			array_push( $bindVars, (int)$pListHash['blog_id'] );
			$joinSql .= " LEFT OUTER JOIN `".BIT_DB_PREFIX."blogs_posts_map` bpm ON ( bpm.`post_content_id` = bp.`content_id` ) ";
			$joinSql .= " LEFT OUTER JOIN `".BIT_DB_PREFIX."blogs` b ON ( bpm.`blog_content_id`=b.`content_id` ) ";
								//	" ON ( b.`content_id` = bpm.`blog_content_id` AND bp.`content_id` = bpm.`post_content_id` )";
			$whereSql .= ' AND b.`blog_id` = ? ';
			$pListHash['sort_mode'] = 'date_added_desc';
		}

		if( @$this->verifyId( $pListHash['post_id_gt'] ) ) {
			array_push( $bindVars, (int)$pListHash['post_id_gt'] );
			$whereSql .= ' AND bp.`post_id` > ? ';
		}

		if( @$this->verifyId( $pListHash['post_id_lt'] ) ) {
			array_push( $bindVars, (int)$pListHash['post_id_lt'] );
			$whereSql .= ' AND bp.`post_id` < ? ';
		}

		if( @$this->verifyId( $pListHash['user_id'] ) ) {
			array_push( $bindVars, (int)$pListHash['user_id'] );
			$whereSql .= ' AND lc.`user_id` = ? ';
		}

		if( @$this->verifyId( $pListHash['group_id'] ) ) {
			array_push( $bindVars, (int)$pListHash['group_id'] );
			$joinSql .= " INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON (ugm.`user_id`=uu.`user_id`)";
			$whereSql .= ' AND ugm.`group_id` = ? ';
		}

		// map user to login in case we used one instead of the other
		if( !empty( $pListHash['user'] ) ) {
			$pListHash['login'] = $pListHash['user'];
		}

		if( !empty( $pListHash['login'] ) ) {
			array_push( $bindVars, $pListHash['login'] );
			$whereSql .= ' AND uu.`login` = ? ';
		}

		if( $pListHash['find'] ) {
			$findesc = '%' . strtoupper( $pListHash['find'] ) . '%';
 			$whereSql .= "AND (UPPER(lc.`data`) like ?) ";
			$bindVars[] =$findesc;
		}

		if( !empty( $pListHash['date'] ) && is_numeric( $pListHash['date'] ) ) {
			$whereSql .= " AND  lc.`created`<=? ";
			$bindVars[]= $pListHash['date'];
		}

		if( !empty( $pListHash['date_start'] ) && is_numeric( $pListHash['date_start'] ) ) {
			$whereSql .= " AND  lc.`created`>=? ";
			$bindVars[]= $pListHash['date_start'];
		}
		if( !empty( $pListHash['date_end'] ) && is_numeric( $pListHash['date_end'] ) ) {
			$whereSql .= " AND  lc.`created`<=? ";
			$bindVars[]= $pListHash['date_end'];
		}

		if( !empty( $pListHash['content_perm_name'] ) ) {
			$this->getContentListPermissionsSql( $pListHash['content_perm_name'], $selectSql, $joinSql, $whereSql, $bindVars );
		}

		/* Check if the post wants to be viewed before / after respective dates
		 * Note: expiring posts are determined by the expired date being greater than the publish date
		 */
		$this->getDateRestrictions($pListHash, $whereSql, $bindVars);

		/* sort_mode is never empty due to call to prepGetList above
		 * I think this will have to be perminently removed and default
		 * set before passing the list hash in if a different default is
		 * desired from that in prepGetList. -wjames5
		 */
		/*
		if( empty( $pListHash['sort_mode'] ) ) {
			$pListHash['sort_mode'] = 'publish_date_desc';
			$sortModePrefix = 'bp';
			//$pListHash['sort_mode'] = 'created_desc';
		} else {
		*/
		$sortModePrefix = '';
		if( !empty( $pListHash['sort_mode'] ) && !strpos( $pListHash['sort_mode'], '.' ) ) {
			switch( $pListHash['sort_mode'] ) {
				case 'publish_date_asc':
				case 'publish_date_desc':
				case 'post_id_desc':
				case 'post_id_asc':
					$sortModePrefix = 'bp.';
					break;
				case 'date_added_desc':
					$sortModePrefix = 'bpm.';
					break;
				case 'hits_asc':
				case 'hits_desc':
					$sortModePrefix = 'lch.';
					break;
				case 'sort_date_asc':
				case 'sort_date_desc':
					break;
				case 'real_name_asc':
				case 'real_name_desc':
					$sortModePrefix = 'uu.';
					break;
				// these technicall are not correct, however, we do not double join on users_users, so we sort by creator real_name
				case 'creator_real_name_asc':
				case 'modifier_real_name_asc':
					$sortModePrefix = 'uu.';
					$pListHash['sort_mode'] = 'real_name_asc';
					break;
				case 'registration_date_desc':
					$sortModePrefix = 'uu.';
					$pListHash['sort_mode'] = 'registration_date_desc';
					break;
				case 'creator_real_name_desc':
				case 'modifier_real_name_desc':
					$sortModePrefix = 'uu.';
					$pListHash['sort_mode'] = 'real_name_desc';
					break;
				default:
					$sortModePrefix = 'lc.';
					break;
			}
		}

		$secondarySortMode = ($pListHash['sort_mode'] != 'last_modified_desc') ? ', last_modified DESC': '';
		$sort_mode = $sortModePrefix . $this->mDb->convertSortmode( $pListHash['sort_mode'] ).$secondarySortMode;

		$query = "
			SELECT
				bp.`post_id`, bp.`publish_date`, bp.`expire_date`, bp.`trackbacks_to`, bp.`trackbacks_from`,
				lc.*, lch.`hits`, lcds.`data` AS `summary`, COALESCE( bp.`publish_date`, lc.`last_modified` ) AS sort_date,
				uu.`email`, uu.`login`, uu.`real_name`,
					lfa.`file_name` as `avatar_file_name`, lfa.`mime_type` AS `avatar_mime_type`, laa.`attachment_id` AS `avatar_attachment_id`,
					lfp.`file_name` AS `image_file_name`, lfp.`mime_type` AS `image_mime_type`, lap.`attachment_id` AS `image_attachment_id`
			FROM `".BIT_DB_PREFIX."blog_posts` bp
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON lc.`content_id`         = bp.`content_id`
				INNER JOIN		`".BIT_DB_PREFIX."users_users`			 uu ON uu.`user_id`			   = lc.`user_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON lc.`content_id`         = lch.`content_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_data` lcds ON (lc.`content_id` = lcds.`content_id` AND lcds.`data_type`='summary')
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`	laa ON (uu.`user_id` = laa.`user_id` AND laa.`attachment_id` = uu.`avatar_attachment_id`)
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`	    lfa ON lfa.`file_id`		   = laa.`foreign_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`  lap ON lap.`content_id`        = lc.`content_id` AND lap.`is_primary` = 'y'
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`        lfp ON lfp.`file_id`           = lap.`foreign_id`
				$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY $sort_mode";

		# Get count of total number of items available
		$query_cant = "
			SELECT COUNT( * )
			FROM `".BIT_DB_PREFIX."blog_posts` bp
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON lc.`content_id` = bp.`content_id`
				INNER JOIN		`".BIT_DB_PREFIX."users_users`			 uu ON uu.`user_id`			   = lc.`user_id`
				$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql ";

		$cant = $this->mDb->getOne($query_cant,$bindVars);
		$pListHash["cant"] = $cant;

		# Check for offset out of range
		if ( $pListHash['offset'] < 0 ) {
			$pListHash['offset'] = 0;
		} elseif ( $pListHash['offset']	> $pListHash["cant"] ) {
			$lastPageNumber = ceil ( $pListHash["cant"] / $pListHash['max_records'] ) - 1;
			$pListHash['offset'] = $pListHash['max_records'] * $lastPageNumber;
		}


		$result = $this->mDb->query($query,$bindVars,$pListHash['max_records'],$pListHash['offset']);
		$ret = array();

		$comment = new LibertyComment();
		while ($res = $result->fetchRow()) {
			$res['no_fatal'] = TRUE;
			$accessError = $this->invokeServices( 'content_verify_access', $res, FALSE );
			if( empty( $accessError ) ) {

				foreach( array( 'avatar', 'image' ) as $img ) {
					$res[$img] = liberty_fetch_thumbnails( array(
						'source_file' => liberty_mime_get_source_file( array( 'user_id'=>$res['user_id'], 'package'=>liberty_mime_get_storage_sub_dir_name( array( 'type' => $res[$img.'_mime_type'], 'name'=>$res[$img.'_file_name'] ) ), 'file_name'=>basename( $res[$img.'_file_name'] ), 'sub_dir'=>$res[$img.'_attachment_id'] ) )
					));
				}
				$res['thumbnail_url'] = BitBlogPost::getImageThumbnails( $res );
				$res['num_comments'] = $comment->getNumComments( $res['content_id'] );
				$res['post_url'] = BitBlogPost::getDisplayUrlFromHash( $res['content_id'], $res );
				$res['display_url'] = $res['post_url'];
				$res['display_link'] = $this->getDisplayLink( $res['title'], $res );
				$res['blogs'] = $this->getBlogMemberships( $res['content_id'] );

				// trackbacks
				if($res['trackbacks_from']!=null)
					$res['trackbacks_from'] = unserialize($res['trackbacks_from']);

				if (!is_array($res['trackbacks_from']))
					$res['trackbacks_from'] = array();

				$res['trackbacks_from_count'] = count(array_keys($res['trackbacks_from']));
				if($res['trackbacks_to']!=null)
					$res['trackbacks_to'] = unserialize($res['trackbacks_to']);
				if ($res['user_id'] == $gBitUser->mUserId) {
					$res['ownsblog'] = 'y';
				} else {
					$res['ownsblog'] = 'n';
				}
				$res['trackbacks_to_count'] = count($res['trackbacks_to']);

				$res['pages'] = $this->getNumberOfPages( $res['data'] );

				// deal with the parsing
				$parseHash['format_guid']     = $res['format_guid'];
				$parseHash['content_id']      = $res['content_id'];
				$parseHash['user_id']		  = $res['user_id'];
				// support for ...split... and auto split
				if( !empty( $pListHash['full_data'] ) ) {
					$parseHash['data'] = $res['data'];
					$res['parsed'] = $this->parseData( $parseHash );
				} else {
					$parseHash['data'] = $res['data'];
					$parseHash['no_cache'] = TRUE;
					$splitArray = $this->parseSplit($parseHash, $gBitSystem->getConfig( 'blog_posts_description_length', 500));
					$res = array_merge($res, $splitArray);
				}
				if( !empty( $this->mInfo['summary'] ) ) {
					$res['summary'] = $parseHash['data'] = $this->mInfo['summary'];
					$parseHash['no_cache'] = TRUE;
					$res['parsed_summary'] = $this->parsedData( $parseHash );
				}
				if( !empty( $res['crosspost_note'] ) ){
					$res['crosspost_note_raw'] = $parseHash['data'] = $res['crosspost_note'];
					$parseHash['no_cache'] = TRUE;
					$res['crosspost_note'] = $this->parseData( $parseHash );
				}

				$ret[] = $res;

			} elseif( !empty( $accessError ) ) {
				if( !empty( $accessError['access_control'] ) ) {
					$res['post_url'] = BitBlogPost::getDisplayUrlFromHash( $res['content_id'], $res );
					$res['display_url'] = $res['post_url'];
					/* this needs to be part of loop that gets all blogs post is in
					$res['blog_url'] = BitBlog::getDisplayUrlFromHash( $res['blog_content_id'] );
					*/
					$res["parsed_data"] = $accessError['access_control'];
					$ret[] = $res;
				}
			} else {
			}
		}

		$pListHash["data"] = $ret;

		LibertyContent::postGetList( $pListHash );

		return $pListHash;
	}

	/**
	 * alters the whereSql and bindVars to limit the posts returned based on the dates
	 * expects the blog_psots table to be aliased as bp
	 */
	function getDateRestrictions( $pListHash, &$whereSql, &$bindVars ) {
		global $gBitSystem, $gBitUser;

		$now = $gBitSystem->getUTCTime();
		if( !empty( $pListHash['show_future'] ) && !empty( $pListHash['show_expired'] ) && $gBitUser->hasPermission( 'p_blog_posts_read_future' ) && $gBitUser->hasPermission( 'p_blog_posts_read_expired' ) ) {
		// this will show all post at once - future, current and expired
		} elseif( !empty( $pListHash['show_future'] ) && $gBitUser->hasPermission( 'p_blog_posts_read_future' ) ) {
			// hide expired posts but show future
			$whereSql .= " AND ( bp.`expire_date` <= bp.`publish_date` OR bp.`expire_date` > ? ) ";
			$bindVars[] = ( int )$now;
		} elseif( !empty( $pListHash['show_expired'] ) && $gBitUser->hasPermission( 'p_blog_posts_read_expired' ) ) {
			// hide future posts but show expired
			$whereSql .= " AND bp.`publish_date` < ?";
			$bindVars[] = ( int )$now;
		} elseif( !empty( $pListHash['get_future'] ) && $gBitUser->hasPermission( 'p_blog_posts_read_future' ) ) {
			// show only future
			$whereSql .= " AND bp.`publish_date` > ?";
			$bindVars[] = ( int )$now;
		} elseif( !empty( $pListHash['get_expired'] ) && $gBitUser->hasPermission( 'p_blog_posts_read_expired' ) ) {
			// show only expired posts
			$whereSql .= " AND bp.`expire_date` < ? AND bp.`expire_date` > bp.`publish_date` ";
			$bindVars[] = ( int )$now;
		} else {
			// hide future and expired posts
			$whereSql .= " AND ((bp.`publish_date` IS NULL AND bp.`expire_date` IS NULL) OR (bp.`publish_date` <= ? AND ((bp.`expire_date` IS NULL) OR ( bp.`expire_date` <= bp.`publish_date` ) OR ( bp.`expire_date` > ? ))))";
			$bindVars[] = ( int )$now;
			$bindVars[] = ( int )$now;
		}
	}

	/**
	 * Get a list of posts that are to be published in the future
	 *
	 * @param array $pParamHash contains listing options - same as getList()
	 * @access public
	 * @return array of posts
	 */
	function getFutureList( &$pParamHash ) {
		$pParamHash['get_future'] = TRUE;
		return( $this->getList( $pParamHash ));
	}


	/**
	 * Get list of posts that have expired and are not displayed on the site anymore
	 *
	 * @param array $pParamHash contains listing options - same as getList()
	 * @access public
	 * @return array of posts
	 */
	function getExpiredList( &$pParamHash ) {
		$pParamHash['get_expired'] = TRUE;
		return( $this->getList( $pParamHash ));
	}


	/**
	 *
	 */
	function addTrackbackFrom( $url, $title = '', $excerpt = '', $blog_name = '') {
		if( $this->isValid() ) {
			$tbs = $this->getTrackbacksFrom( $this->mPostId );
			$aux = array(
				'title' => $title,
				'excerpt' => $excerpt,
				'blog_name' => $blog_name
			);

			$tbs[$url] = $aux;
			$st = serialize($tbs);
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from`=? where `post_id`=?";
			$this->mDb->query( $query, array( $st, $this->mPostId ) );
			return true;
		}
	}

	/**
	 *
	 */
	function getTrackbacksFrom() {
		if( $this->isValid() ) {
			$st = $this->mDb->getOne("select `trackbacks_from` from `".BIT_DB_PREFIX."blog_posts` where `post_id`=?",array( $this->mPostId ) );
			return unserialize($st);
		}
	}

	/**
	 *
	 */
	function getTrackbacksTo() {
		if( $this->isValid() ) {
			$st = $this->mDb->getOne("select `trackbacks_to` from `".BIT_DB_PREFIX."blog_posts` where `post_id`=?", array( $this->mPostId ) );
			return unserialize($st);
		}
	}

	/**
	 *
	 */
	function clearTrackbacksFrom() {
		if( $this->isValid() ) {
			$empty = serialize(array());
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_from` = ? where `post_id`=?";
			$this->mDb->query( $query, array( $empty, $this->mPostId ) );
		}
	}

	/**
	 *
	 */
	function clearTrackbacksTo() {
 		if( $this->isValid() ) {
 			$empty = serialize(array());
			$query = "update `".BIT_DB_PREFIX."blog_posts` set `trackbacks_to` = ? where `post_id`=?";
			$this->mDb->query( $query, array( $empty, $this->mPostId ) );
		}
	}

	function getViewTemplate( $pAction ){
		$ret = null;
		switch ( $pAction ){
			case "view":
				$ret = "bitpackage:liberty/center_".$pAction."_generic.tpl";
				break;
			case "list":
				$ret = "bitpackage:blogs/center_".$pAction."_blog_posts.tpl";
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
	 *
	 * NOTE: pUserMinimum and pUserMaximum are currently NOT inclusive in parent funtion, so these are one beyond the limit we desire
	 */
	function getAvailableContentStatuses( $pUserMinimum=-6, $pUserMaximum=51 ) {
		global $gBitUser;
		$ret = LibertyMime::getAvailableContentStatuses( $pUserMinimum, $pUserMaximum );
		// this is a little ugly as we manually trim the list to just what we need for blog posts for regular users
		if( !$gBitUser->hasPermission( 'p_liberty_edit_all_status' )) {
			if ( array_key_exists( -1, $ret ) ){
				unset( $ret[-1] );
			}
			if ( array_key_exists( 50, $ret ) && $ret[50]=="Available" ){
				$ret[50] = "Public";
			}
		}
		return $ret;
	}

	/**
	* Returns the create/edit url to a blog post
	* @param number $pContentId a valid content id
	* @param array $pMixed a hash of params to add to the url
	*/
	function getEditUrl( $pContentId = NULL, $pMixed = NULL ){
		if( @BitBase::verifyId( $pContentId ) ) {
			$ret = BLOGS_PKG_URL.'post.php?content_id='.$pContentId;
		} elseif( $this->isValid() ) {
			$ret = BLOGS_PKG_URL.'post.php?content_id='.$this->mContentId;
		} else {
			$ret = BLOGS_PKG_URL.'post.php'.(!empty( $pMixed )?"?":"");
		}
		foreach( $pMixed as $key => $value ){
			if( $key != "content_id" || ( $key == "content_id" && @BitBase::verifyId( $value ) ) ) {
				$ret .= (isset($amp)?"&":"").$key."=".$value;
			}
			$amp = TRUE;
		}
		return $ret;
	}

}
?>
