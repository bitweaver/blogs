{strip}

<div class="floaticon">{bithelp}</div>

<div class="listing blogs">
	<div class="header">
		<h1>{tr}Blogs{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		<div class="navbar">
			<ul>
				<li>{biticon ipackage="icons" iname="emblem-symbolic-link" iexplain="sort by"}</li>
				{if $gBitSystem->isFeatureActive( 'blog_list_title' )}
					<li>{smartlink ititle="Title" isort="title" offset=$offset}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'blog_list_created' )}
					<li>{smartlink ititle="Created" isort="created" iorder=desc offset=$offset}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'blog_list_lastmodif' )}
					<li>{smartlink ititle="Last Modified" isort="last_modified" iorder=desc idefault=1 offset=$offset}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'blog_list_user' )}
					<li>{smartlink ititle="Creator" isort="user" offset=$offset}</li>
				{/if}
{* DEPRECATED - need an alt since posts col is being eliminated - need way to sort on postscant -wjames5
				{if $gBitSystem->isFeatureActive( 'blog_list_posts' )}
					<li>{smartlink ititle="Posts" isort="posts" iorder=desc offset=$offset}</li>
				{/if}
*}
				{if $gBitSystem->isFeatureActive( 'blog_list_visits' )}
					<li>{smartlink ititle="Visits" isort="hits" iorder=desc offset=$offset}</li>
				{/if}
{* TODO: Add back once activity is implemented
				{if $gBitSystem->isFeatureActive( 'blog_list_activity' )}
					<li>{smartlink ititle="Activity" isort="activity" iorder=desc offset=$offset}</li>
				{/if}
*}
			</ul>
		</div>

		<ul class="clear data">
			{foreach from=$blogsList item=listBlog key=blogContentId}
				<li class="item {cycle values='odd,even'}">
					<div class="floaticon">
						{if $gBitUser->hasPermission( 'p_liberty_assign_content_perms' ) and $listBlog.content_id}
							{if $gContent->mPerms} {* org note from liberty:service_content_icon_inc: don't think there is a serviceHash way of working out if there are individual permissions set *}
								{assign var=perm_icon value="icons/emblem-readonly"}
							{else}
								{assign var=perm_icon value="icons/emblem-shared"}
							{/if}
							{if $role_model }
								{smartlink ipackage=liberty ifile=content_role_permissions.php ititle="Assign Permissions" ibiticon=$perm_icon ipackage=liberty ifile="content_permissions.php" content_id=$listBlog.content_id}
							{else}
								{smartlink ipackage=liberty ifile=content_permissions.php ititle="Assign Permissions" ibiticon=$perm_icon ipackage=liberty ifile="content_permissions.php" content_id=$listBlog.content_id}
							{/if}
						{/if}

						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) || ($gBitUser->hasPermission( 'p_blogs_admin' )) or ($listBlog.is_public eq 'y')}
									<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$listBlog.blog_id}">{booticon iname="icon-edit" ipackage="icons" iexplain="post"}</a>
						{/if}
						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
							<a title="{tr}edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$listBlog.blog_id}">{biticon ipackage="icons" iname="document-properties" iexplain="configure"}</a>
						{/if}
						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
							<a title="{tr}remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove=1&amp;blog_id={$listBlog.blog_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="delete"}</a>
						{/if}
					</div>

					{if $gBitSystem->isFeatureActive( 'blog_list_title' )}
						<h2><a title="{$listBlog.title|escape}" href="{$listBlog.blog_url}">{$listBlog.title|escape}</a></h2>
					{/if}

					{if $gBitSystem->isFeatureActive( 'blog_list_description' )}
						<p>{$listBlog.parsed}</p>
					{/if}

					<div class="date">
						{if $gBitSystem->isFeatureActive( 'blog_list_user' )}
						{if $gBitSystem->getConfig('blog_list_user_as') eq 'link'}
								{tr}Created by {$listBlog.user|userlink}{/tr}
							{elseif $gBitSystem->getConfig('blog_list_user_as') eq 'avatar'}
								{$listBlog.user|avatarize}
							{else}
								{tr}Created by {$listBlog.user}{/tr}
							{/if}
						{/if}

						{if $gBitSystem->isFeatureActive( 'blog_list_created' )}
							{tr}{if !$gBitSystem->isFeatureActive('blog_list_user')}<br />Created{/if} on {$listBlog.created|bit_short_date}{/tr}
							<br />
						{/if}

						{if $gBitSystem->isFeatureActive( 'blog_list_lastmodif' )}
							{tr}Last Modified{/tr} {$listBlog.last_modified|bit_short_datetime}
						{/if}
					</div>

					<div class="footer">
						{if $gBitSystem->isFeatureActive( 'blog_list_posts' )}
							{tr}Posts{/tr}: {$listBlog.postscant}&nbsp;&bull;&nbsp;
						{/if}

						{if $gBitSystem->isFeatureActive( 'blog_list_visits' )}
							{tr}Visits{/tr}: {$listBlog.hits}&nbsp;&bull;&nbsp;
						{/if}
					</div>

					<div class="clear"></div>
				</li>
			{foreachelse}
				<li class="item norecords">
					{tr}No records found{/tr}
				</li>
			{/foreach}
		</ul>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
