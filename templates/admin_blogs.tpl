{strip}
{form}
	{jstabs}
		{jstab title="Home Blog"}
			{legend legend="Home Blog"}
				<input type="hidden" name="page" value="{$page}" />
				<div class="row">
					{formlabel label="Home Blog (main blog)" for="homeBlog"}
					{forminput}
						<select name="homeBlog" id="homeBlog">
							{section name=ix loop=$blogs}
								<option value="{$blogs[ix].blog_id|escape}" {if $blogs[ix].blog_id eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"...":true}</option>
							{sectionelse}
								<option>{tr}No records found{/tr}</option>
							{/section}
						</select>
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="homeTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Blog Features"}
			{legend legend="Blog Features"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formBlogFeatures key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=`$gBitSystemPrefs.$item` labels=false id=$item}
							{formhelp note=`$output.help` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row">
					{formlabel label="Default ordering for blog listing" for="blog_list_order"}
					{forminput}
						<select name="blog_list_order" id="blog_list_order">
							<option value="created_desc" {if $blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date{/tr} ({tr}desc{/tr})</option>
							<option value="last_modified_desc" {if $blog_list_order eq 'last_modified_desc'}selected="selected"{/if}>{tr}Last modification date{/tr} ({tr}desc{/tr})</option>
							<option value="title_asc" {if $blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title{/tr} ({tr}asc{/tr})</option>
							<option value="posts_desc" {if $blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts{/tr} ({tr}desc{/tr})</option>
							<option value="hits_desc" {if $blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits{/tr} ({tr}desc{/tr})</option>
							<option value="activity_desc" {if $blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity{/tr} ({tr}desc{/tr})</option>
						</select>
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="In blog listing show user as" for="blog_list_user_as"}
					{forminput}
						<select name="blog_list_user" id="blog_list_user_as">
							<option value="text" {if $blog_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
							<option value="link" {if $blog_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
							<option value="avatar" {if $blog_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
						</select>
					{/forminput}
				</div>

				{if $gBitSystemPrefs.package_categories eq 'y'}
				<div class="row">
					{formlabel label="Use a category for posts" for="blog_categ"}
					{forminput}
						<input type="checkbox" name="blog_categ" id="blog_categ"
							{if $blog_categ eq 'y'}checked="checked"{/if} />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Choose a parent category for blogs" for="blog_parent_categ"}
					{forminput}
						<select name="blog_parent_categ" id="blog_parent_categ">
							<option value="0" {if $blog_parent_categ eq '0'}selected="selected"{/if}>{tr}Top{/tr}</option>
							{section name=i loop=$categs}
								<option value="{$categs[i].category_id}"{if $blog_parent_categ eq $categs[i].category_id} selected="selected"{/if}>{$categs[i].name}</option>
							{/section}
						</select>
					{/forminput}
				</div>
				{/if}

				<div class="row submit">
					<input type="submit" name="featuresTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="List Settings"}
			{legend legend="List Settings"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formBlogLists key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=`$gBitSystemPrefs.$item` labels=false id=$item}
							{formhelp note=`$output.help` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row submit">
					<input type="submit" name="listTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Blog Comments Settings"}
			{legend legend="Blog Comments Settings"}
				<div class="row">
					{formlabel label="Post level comments" for="feature_blogposts_comments"}
					{forminput}
						<input type="checkbox" name="feature_blogposts_comments" id="feature_blogposts_comments" {if $gBitSystemPrefs.feature_blogposts_comments eq 'y'}checked="checked"{/if} />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Default number of comments per page" for="blog_comments_per_page"}
					{forminput}
						<input size="5" type="text" name="blog_comments_per_page" id="blog_comments_per_page" value="{$blog_comments_per_page|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Comments default ordering" for="blog_comments_default_ordering"}
					{forminput}
						<select name="blog_comments_default_ordering" id="blog_comments_default_ordering">
							<option value="comment_date_desc" {if $blog_comments_default_ordering eq 'comment_date_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="comment_date_asc" {if $blog_comments_default_ordering eq 'comment_date_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
							<option value="points_desc" {if $blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
						</select>
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="commentTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}

{/strip}
