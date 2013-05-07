{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTwitter|ucfirst}: {$lblWidgets}</h2>

	{option:showTwitterAdd}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showTwitterAdd}
</div>

{option:datagrid}
	<div class="datagridHolder">
		{$datagrid}
	</div>
{/option:datagrid}

{option:!datagrid}<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>{/option:!datagrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}