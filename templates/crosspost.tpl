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
						{if $availableBlogs}
							<div class="row">
								{formlabel label="Include in Blogs" for="blog_id"}
								{forminput}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											<input name="blog_content_id[]" type="checkbox" option value="{$blogContentId}" {if $gContent->mInfo.blogs.$blogContentId}checked="checked"{/if}>{$availBlogTitle|escape}</option><br/>
										{/foreach}
										{formhelp note="You can cross post to any and all of the blogs listed above.<br />Just check off the blogs you wish this post to also show up in."}
								{/forminput}
							</div>
						{/if}
						
						
						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
							<input type="submit" name="save_post_exit" value="{tr}Save{/tr}" />
						</div>
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
				<a href="{$post_info.url}">{tr}Permalink{/tr}</a>
				{tr}referenced by{/tr} {$post_info.trackbacks_from_count} {tr}posts{/tr} | {tr}references{/tr} {$post_info.trackbacks_to_count} {tr}posts{/tr}
				{if $post_info.allow_comments eq 'y' and $gBitSystem->isFeatureActive( 'blog_posts_comments' )}
					| {$post_info.num_comments} {tr}comments{/tr}
				{/if}
			</div> {* end .footer *}
			
		</div> {* end .blog *}
	</div>	
</div>
{/strip}