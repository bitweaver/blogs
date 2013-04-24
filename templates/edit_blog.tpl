{* $Header$ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="edit blogs">
	<div class="header">
		<h1>{if $gContent->isValid()}{tr}Edit Blog{/tr}{else}{tr}Create Blog{/tr}{/if}</h1>
	</div>

	<div class="body">
		{if $smarty.request.preview}
			<div class="preview">
				<div class="header">
					<h1>{$title}</h1>
					{if $parsed}<h2>{$parsed}</h2>{/if}
					<div class="date">
						{tr}By{/tr}: {displayname user=$user_name}, {if $created}{$created|bit_short_datetime}{else}{$gContent->getField('created')|bit_short_datetime}{/if}<br />
					</div>
				</div>
			</div>
		{/if}

		{form ipackage="blogs" ifile="edit.php"}
			{jstabs}
				{jstab title="Blog Settings"}
					{legend legend="Blog Settings"}
						<input type="hidden" name="blog_id" value="{$gContent->getField('blog_id')}" />
						<input type="hidden" name="content_id" value="{$gContent->getField('content_id')}" />
						<div class="control-group">
							{formfeedback warning=$warning}
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{if $gContent->getTitle()}{$gContent->getTitle()|escape}{else}{displayname hash=$gBitUser->mInfo nolink=FALSE}'s Blog{/if}" />
							{/forminput}
						</div>

						{textarea edit=$edit|default:$gContent->getField('data')}

						<div class="control-group">
							{formlabel label="Number of posts to show" for="max_posts"}
							{forminput}
								<input type="text" name="max_posts" id="max_posts" value="{$gContent->getField('max_posts')|escape|default:10}" />
								{formhelp note='Enter the number of blog posts you wish to display when viewing this blog.'}
							{/forminput}
						</div>

{* DEPRECATED - Slated for removal  -wjames5
						{if $gBitUser->hasPermission('p_blogs_create_is_public')}
						<div class="control-group">
							<label class="checkbox">
								<input type="checkbox" name="is_public" id="is_public" {if $gContent->getField('is_public') eq 'y'}checked="checked"{/if} />Public
								{formhelp note='Allow other user to post in this blog'}
							</label>
						</div>
						{/if}
*}

						<div class="control-group">
							<label class="checkbox">
								<input type="checkbox" name="use_title" id="use_title" {if !$gContent->isValid() || $gContent->getField('use_title') eq 'y'}checked="checked"{/if} />Use titles in blog posts
								{formhelp note='If this is not selected, the time and date of when the post was created will be displayed instead of the post title.'}
							</label>
						</div>

						<div class="control-group">
							<label class="checkbox">
								<input type="checkbox" name="use_find" id="use_find" {if !$gContent->isValid() || $gContent->getField('use_find') eq 'y'}checked="checked"{/if} />Allow search
								{formhelp note='Allow users to search this blog for occurances of words.'}
							</label>
						</div>

						<div class="control-group">
							<label class="checkbox">
								<input type="checkbox" name="allow_comments" id="allow_comments" {if !$gContent->isValid() || $gContent->getField('allow_comments') eq 'y'}checked="checked"{/if} />Allow comments
								{formhelp note='Are other users allowed to add comments to posts made in this blog?'}
							</label>
						</div>

						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

						<div class="control-group submit">
							<input type="submit" class="btn" name="preview" value="{tr}preview{/tr}" />&nbsp;
							<input type="submit" class="btn" name="save_blog" value="{tr}save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
