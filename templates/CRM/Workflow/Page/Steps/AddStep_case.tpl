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
        <label>{ts}Include Profile{/ts}</label>
        <input class="crm-profile-selector crm-form-text" data-group-type="Case" data-entities='{$profilesDataEntities}' name="SWCaseIncludeProfile" type="text" id="SWCaseIncludeProfile">
    </div>

    <div class="SWStepField">
        <label>{ts}Breadcrumb{/ts}:</label>
        <input id="SWCaseBreadcrumb" />
    </div>

    <p><button id="AddCase" class="AddStepButton" data-step-type="Case">{ts}Add Case Form to Workflow{/ts}</button></p>
</div>