{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add}
	<div class="box">
		<div class="heading">
			<h3>{$lblTwitter|ucfirst}: {$lblAdd}</h3>
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
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}