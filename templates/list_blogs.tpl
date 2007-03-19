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
				{if $blog_list_title eq 'y'}
					<li>{smartlink ititle="Title" isort="title" offset=$offset}</li>
				{/if}
				{if $blog_list_created eq 'y'}
					<li>{smartlink ititle="Created" isort="created" iorder=desc offset=$offset}</li>
				{/if}
				{if $blog_list_lastmodif eq 'y'}
					<li>{smartlink ititle="Last Modified" isort="last_modified" iorder=desc idefault=1 offset=$offset}</li>
				{/if}
				{if $blog_list_user eq 'y'}
					<li>{smartlink ititle="Creator" isort="user" offset=$offset}</li>
				{/if}
{* DEPRECATED - need an alt since posts col is being eliminated - need way to sort on postscant -wjames5
				{if $blog_list_posts eq 'y'}
					<li>{smartlink ititle="Posts" isort="posts" iorder=desc offset=$offset}</li>
				{/if}
*}
				{if $blog_list_visits eq 'y'}
					<li>{smartlink ititle="Visits" isort="hits" iorder=desc offset=$offset}</li>
				{/if}
{* TODO: Add back once activity is implemented
				{if $blog_list_activity eq 'y'}
					<li>{smartlink ititle="Activity" isort="activity" iorder=desc offset=$offset}</li>
				{/if}
*}
			</ul>
		</div>

		<ul class="clear data">
			{foreach from=$blogsList item=listBlog key=blogContentId}
				<li class="item {cycle values='odd,even'}">
					<div class="floaticon">
						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) || ($gBitUser->hasPermission( 'p_blogs_admin' )) or ($listBlog.is_public eq 'y')}
									<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$listBlog.blog_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="post"}</a>
						{/if}
						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
							<a title="{tr}edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$listBlog.blog_id}">{biticon ipackage="icons" iname="document-properties" iexplain="configure"}</a>
						{/if}
						{if ($gBitUser->mUserId and $listBlog.user_id eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'p_blogs_admin' )}
							<a title="{tr}remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove=1&amp;blog_id={$listBlog.blog_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
						{/if}
					</div>

					{if $blog_list_title eq 'y'}
						<h2><a title="{$listBlog.title|escape}" href="{$listBlog.blog_url}">{$listBlog.title|escape}</a></h2>
					{/if}

					{if $blog_list_description eq 'y'}
						<p>{$listBlog.parsed}</p>
					{/if}

					<div class="date">
						{if $blog_list_user eq 'y'}
							{if $blog_list_user_as eq 'link'}
								{tr}Created by {$listBlog.user|userlink}{/tr}
							{elseif $blog_list_user_as eq 'avatar'}
								{$listBlog.user|avatarize}
							{else}
								{tr}Created by {$listBlog.user}{/tr}
							{/if}
						{/if}

						{if $blog_list_created eq 'y'}
							{tr}{if $blog_list_user ne 'y'}<br />Created{/if} on {$listBlog.created|bit_short_date}{/tr}
							<br />
						{/if}

						{if $blog_list_lastmodif eq 'y'}
							{tr}Last Modified {$listBlog.last_modified|bit_short_datetime}{/tr}
						{/if}
					</div>

					<div class="footer">
						{if $blog_list_posts eq 'y'}
							{tr}Posts{/tr}: {$listBlog.postscant}&nbsp;&bull;&nbsp;
						{/if}
						
						{if $blog_list_visits eq 'y'}
							{tr}Visits{/tr}: {$listBlog.hits}&nbsp;&bull;&nbsp;
						{/if}
					</div>

					<div class="clear"></div>
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
