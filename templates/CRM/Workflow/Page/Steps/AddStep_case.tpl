<h3 class="SWToggleFormTrigger">{ts}Add a Case{/ts}:</h3>
<div class="SWToggleForm">

    <div class="SWStepField">
        <label>{ts}Case Type{/ts}:</label>
        <select id="SWCaseSelector">
            {foreach from=$caseTypes item=type}
                <option value="{$type.id}">{$type.title}</option>
            {/foreach}
        </select>
    </div>

    <div class="SWStepField">
        <input type="checkbox" id="SWCaseIncludeProfile" checked="checked" />
        <label>{ts}Include Associated Profile/custom data if present{/ts}</label>
    </div>

    <div class="SWStepField">
        <label>{ts}Breadcrumb{/ts}:</label>
        <input id="SWCaseBreadcrumb" />
    </div>

    <p><button id="AddCase" class="AddStepButton" data-step-type="Case">{ts}Add Case Form to Workflow{/ts}</button></p>
</div>