{strip}
<div class="edit crosspost">
	<div class="header">
		<h1>{tr}Crosspost{/tr}</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" name="blogpost" id="editpageform"}
			<input type="hidden" name="post_id" value="{$post_id|escape}" />
			<input type="hidden" name="rows" value="{$rows}"/>
			<input type="hidden" name="cols" value="{$cols}"/>			
					{legend legend="Crosspost"}
						{* we loop over this twice because we want two separate lists from the same hash *}
						{if $availableBlogs}
							<div class="row">
								{formlabel label="Blogs this Post is Already Crossposted To" for="blog_id"}
								{forminput}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											{if $gContent->mInfo.blogs.$blogContentId && ($blogContentId != $crosspost.blog_content_id) }
												{assign var="has_crosspost" value=TRUE}
												{$availBlogTitle|escape}
												&nbsp;<a title="{tr}Edit{/tr}" href="{$smarty.const.BLOGS_PKG_URL}crosspost.php?blog_content_id={$blogContentId}&amp;post_id={$post_info.post_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="edit crosspost note"}</a>
												&nbsp;<a title="{tr}Remove{/tr}" href="{$smarty.const.BLOGS_PKG_URL}crosspost.php?action=remove&amp;post_id={$post_info.post_id}&amp;blog_content_id={$blogContentId}&amp;status_id=300">{biticon ipackage="icons" iname="edit-delete" iexplain="delete this crossposting"}</a><br/>
											{/if}
										{/foreach}
										{if !$has_crosspost}
											{formhelp note="This blog post has not been crossposted to any blogs yet."}
										{else}
											{formhelp note=""}
										{/if}
								{/forminput}
							</div><br/>
						{/if}
					
						{if $availableBlogs}
							<div class="row">
								{formlabel label="Include in Blogs" for="blog_id"}
								{forminput}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											{if !$gContent->mInfo.blogs.$blogContentId || ($blogContentId == $crosspost.blog_content_id) }
												{assign var="has_crosspost_option" value=TRUE}
												<input name="blog_content_id[]" type="checkbox" option value="{$blogContentId}" {if $blogContentId == $crosspost.blog_content_id}checked="checked"{/if}>{$availBlogTitle|escape}</option><br/>
											{/if}
										{/foreach}
										{if !$has_crosspost_option}
											{formhelp note="This blog post has been crossposted to all blogs you have permission to cross post to."}
										{else}
											{formhelp note="You can cross post to any and all of the blogs listed above.<br />Just check off the blogs you wish this post to also show up in."}
										{/if}
								{/forminput}
							</div>
						{/if}
						
						{if $has_crosspost_option}
						{formlabel label="Crosspost Note Format"}
							{forminput}
								{foreach name=formatPlugins from=$gLibertySystem->mPlugins item=plugin key=guid}
									{if $plugin.edit_field eq $post_info.format_guid}
										{$plugin.edit_label}	
									{/if}
								{/foreach}
							{/forminput} 
							{textarea id="crosspost_note" label="Crosspost Note (Optional)" name="crosspost_note" noformat="y" rows=6 help="Add a note you would like to appear above the post when viewed on the crossposted blog. This does not appear on the post page."}{$crosspost.crosspost_note}{/textarea}
											
							<div class="row submit">
								<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
								<input type="submit" name="save_post_exit" value="{tr}Save{/tr}" />
							</div>
						{/if}
					{/legend}
		{/form}


		
		{* ------this is the same as the guts of view_blog_post---- *}
		<div class="display blogs">
			<div class="header">
		
				<h1>
					{$post_info.blogtitle|escape}
				</h1>
		
				<div class="navigation">
					{if $gContent_previous}
						<span class="left">
							Previous: <a href="{$gContent_previous->getDisplayUrl()}">{$gContent_previous->mInfo.title|escape}</a> 
						</span>
					{/if}
		
					{if $gContent_next}
						<span class="right">
							Next: <a href="{$gContent_next->getDisplayUrl()}">{$gContent_next->mInfo.title|escape}</a> 
						</span>
					{/if}
				</div>
		
				<h1>
					{if $post_info.use_title eq 'y'}
						{$post_info.title|escape}
					{else}
						{$post_info.publish_date|default:$post_info.created|bit_long_date}
					{/if}
				</h1>
		
				<div class="date">
					{tr}Posted by{/tr} {displayname hash=$post_info}<br />
					{tr}Posted on{/tr} {$post_info.publish_date|default:$post_info.created|bit_long_date}<br/>			
					{if count($post_info.blogs) > 0}
						{tr}Posted to{/tr}
						{foreach from=$post_info.blogs item=memberBlog key=blogContentId name=memberBlogLoop}
							<a href="{$memberBlog.blog_url}">{$memberBlog.title}</a>{if $smarty.foreach.memberBlogLoop.total > 1 && !$smarty.foreach.memberBlogLoop.last }, {/if}
						{/foreach}
					<br />
					{/if}	
				</div>
			</div>
		
			<div class="body">
				<div class="content">
					{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$post_info}
		
					{$parsed_data}
				</div> <!-- end .content -->
			</div> <!-- end .body -->
			
			<div class="footer">
				<a href="{$post_info.display_url}" rel="bookmark">{tr}Permalink{/tr}</a>
				{tr}referenced by{/tr} {$post_info.trackbacks_from_count} {tr}posts{/tr} | {tr}references{/tr} {$post_info.trackbacks_to_count} {tr}posts{/tr}
				{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blog_posts_comments' )}
					| {$post_info.num_comments} {tr}comments{/tr}
				{/if}
			</div> {* end .footer *}
			
		</div> {* end .blog *}
	</div>	
</div>
{/strip}
