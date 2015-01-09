{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblTwitter}</h2>
</div>

{option:noConsumerKeys}
    <div class="generalMessage infoMessage content">
        <p><strong>{$msgConfigurationError}</strong></p>
        <ul class="pb0">
            {option:noConsumerKey}
                <li>{$errNoConsumerKey}</li>{/option:noConsumerKey}
            {option:noConsumerSecret}
                <li>{$errNoConsumerSecret}</li>{/option:noConsumerSecret}
        </ul>
    </div>
{/option:noConsumerKeys}

{form:settings}
    <div class="box horizontal">
        <div class="heading">
            <h3>{$lblApplication|ucfirst}</h3>
        </div>
        <div class="options">
            {$msgTwitterHelp}
        </div>
        <div class="options">
            <p>
                <label for="consumerKey">{$lblConsumerKey|ucfirst}</label>
                {$txtConsumerKey} {$txtConsumerKeyError}
            </p>

            <p>
                <label for="consumerSecret">{$lblConsumerSecret|ucfirst}</label>
                {$txtConsumerSecret} {$txtConsumerSecretError}
            </p>
        </div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="save" class="inputButton button mainButton" type="submit" name="save"
                   value="{$lblSave|ucfirst}"/>
        </div>
    </div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
