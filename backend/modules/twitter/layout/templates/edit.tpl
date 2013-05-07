{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="box">
		<div class="heading">
			<h3>{$lblTranslations|ucfirst}: {$lblEditWidget}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="username">{$lblUsername|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtUsername} {$txtUsernameError}
					<span class="helpTxt">{$msgUsernameHelp}</span>
				</p>
				<p>
					<label for="tag">{$lblTag|ucfirst}</label>
					{$txtTag} {$txtTagError}
					<span class="helpTxt">{$msgTagHelp}</span>
				</p>
				<p>
					<label for="number_of_items">{$lblNumberOfItems|ucfirst}</label>
					{$ddmNumberOfItems} {$ddmNumberOfItemsError}
					<span class="helpTxt">{$msgNumberOfItemsHelp}</span>
				</p>
			</fieldset>
		</div>

		<div class="fullwidthOptions">
			{option:showTwitterDelete}
			<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
			<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
				<p>
					{$msgConfirmDelete}
				</p>
			</div>
			{/option:showTwitterDelete}

			<div class="buttonHolderRight">
				<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
			</div>
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}