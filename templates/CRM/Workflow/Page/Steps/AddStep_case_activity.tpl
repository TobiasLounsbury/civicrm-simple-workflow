<h3 class="SWToggleFormTrigger">{ts}Add a Case Activity{/ts}:</h3>
<div class="SWToggleForm">

    <div class="SWStepField">
        <label>{ts}Activity Type{/ts}:</label>
        <select id="SWCaseActivitySelector">
            {foreach from=$caseActivityTypes item=type}
                <option value="{$type.value}">{$type.label}</option>
            {/foreach}
        </select>
    </div>

    <div class="SWStepField">
        <label>{ts}Breadcrumb{/ts}:</label>
        <input id="SWCaseActivityBreadcrumb" />
    </div>

    <p><button id="AddCaseActivity" class="AddStepButton" data-step-type="CaseActivity">{ts}Add Case Activity Form to Workflow{/ts}</button></p>
</div>