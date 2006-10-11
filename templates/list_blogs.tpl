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
				{if $blog_list_posts eq 'y'}
					<li>{smartlink ititle="Posts" isort="posts" iorder=desc offset=$offset}</li>
				{/if}
				{if $blog_list_visits eq 'y'}
					<li>{smartlink ititle="Visits" isort="hits" iorder=desc offset=$offset}</li>
				{/if}
				{if $blog_list_activity eq 'y'}
					<li>{smartlink ititle="Activity" isort="activity" iorder=desc offset=$offset}</li>
				{/if}
			</ul>
		</div>

		<ul class="clear data">
			{section name=changes loop=$listpages}
				<li class="item {cycle values='odd,even'}">
					<div class="floaticon">
						{if $gBitUser->hasPermission( 'p_blogs_post' )}
							{if ($gBitUser->isAdmin()) or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_gBitUser->hasPermission( 'p_blogs_post' ))}
								{if ($gBitUser->mUserId and $listpages[changes].user_id eq $gBitUser->mUserId) or ($gBitUser->hasPermission( 'p_blogs_admin' )) or ($listpages[changes].is_public eq 'y')}
									<a title="{tr}post{/tr}" href="{$smarty.const.BLOGS_PKG_URL}post.php?blog_id={$listpages[changes].blog_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="post"}</a>
								{/if}
							{/if}
						{/if}
						{if ($gBitUser->mUserId and $listpages[changes].user_id eq $gBitUser->mUserId) or ($gBitUser->hasPermission( 'p_blogs_admin' ))}
							{if ($gBitUser->isAdmin()) or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_gBitUser->hasPermission( 'p_blogs_create' ))}
								<a title="{tr}edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}edit.php?blog_id={$listpages[changes].blog_id}">{biticon ipackage="icons" iname="document-properties" iexplain="configure"}</a>
							{/if}
						{/if}
						{if ($gBitUser->mUserId and $listpages[changes].user_id eq $gBitUser->mUserId) or ($gBitUser->hasPermission( 'p_blogs_admin' ))}
							{if ($gBitUser->isAdmin()) or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_gBitUser->hasPermission( 'p_blogs_create' ))}
								<a title="{tr}remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove=1&amp;blog_id={$listpages[changes].blog_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
							{/if}
						{/if}
						{if $gBitUser->isAdmin()}
						    <a title="{tr}perms{/tr}" href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=blog%20{$listpages[changes].title|escape}&amp;objectType=blog&amp;permType=blogs&amp;object_id={$listpages[changes].blog_id}">{if $listpages[changes].individual eq 'y'}{biticon ipackage="icons" iname="emblem-readonly" iexplain="set permissions"}{else}{biticon ipackage="icons" iname="emblem-shared" iexplain="set permissions"}{/if}</a>
						{/if}
					</div>

					{if $blog_list_title eq 'y'}
						<h2>{if ($gBitUser->isAdmin()) or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_gBitUser->hasPermission( 'p_blogs_view' ))}<a title="{$listpages[changes].title|escape}" href="{$listpages[changes].blog_url}">{/if}
						{$listpages[changes].title|escape}{if ($gBitUser->isAdmin()) or ($listpages[changes].individual eq 'n') or ($listpages[changes].individual_gBitUser->hasPermission( 'p_blogs_view' ))}</a>{/if}</h2>
					{/if}

					{if $blog_list_description eq 'y'}
						<p>{$listpages[changes].parsed}</p>
					{/if}

					<div class="date">
						{if $blog_list_user eq 'y'}
							{if $blog_list_user eq 'link'}
								{tr}Created by {$listpages[changes].user|userlink}{/tr}
							{elseif $blog_list_user eq 'avatar'}
								{$listpages[changes].user|avatarize}
							{else}
								{tr}Created by {$listpages[changes].user}{/tr}
							{/if}
						{/if}

						{if $blog_list_created eq 'y'}
							{tr}{if $blog_list_user ne 'y'}<br />Created{/if} on {$listpages[changes].created|bit_short_date}{/tr}
							<br />
						{/if}

						{if $blog_list_lastmodif eq 'y'}
							{tr}Last Modified {$listpages[changes].last_modified|bit_short_datetime}{/tr}
						{/if}
					</div>

					<div class="footer">
						{if $blog_list_posts eq 'y'}
							{tr}Posts{/tr}: {$listpages[changes].posts}&nbsp;&bull;&nbsp;
						{/if}
						
						{if $blog_list_visits eq 'y'}
							{tr}Visits{/tr}: {$listpages[changes].hits}&nbsp;&bull;&nbsp;
						{/if}
						
						{if $blog_list_activity eq 'y'}
							{tr}Activity{/tr}: {$listpages[changes].activity|default:0}
						{/if}
					</div>

					<div class="clear"></div>
				<li>
			{sectionelse}
				<li class="item norecords">
					{tr}No records found{/tr}
				</li>
			{/section}
		</ul>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
