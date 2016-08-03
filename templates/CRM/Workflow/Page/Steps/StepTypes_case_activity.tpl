<div id="SimpleWorkflowTypeTemplateCaseActivity">
    <input type ="hidden" name="data[#ORDER#][entity_id]" class="entity_id" />

    <div class="SWStepField">
        <label>{ts}Include Profile{/ts}:</label>
        <input data-entities='{$profilesDataEntities}' name="data[#ORDER#][options][include_profile]" type="text" class="case_activity_option_include_profile">
    </div>

    <div class="SWStepField">
        <label>{ts}Case Step{/ts}:</label>
        <input type="hidden" class="SW-Relationship-Contact-Hidden case_activity_options_case_order" name="data[#ORDER#][options][case_order]" />
        <select class="SW-Relationship-Contact"></select>
    </div>

    <div class="SWStepField">
        <label>{ts}Activity Status{/ts}:</label>
        <select name="data[#ORDER#][options][status]" class="case_activity_options_status">
            {foreach from=$activityStatus item=status}
                <option value="{$status.value}">{$status.label}</option>
            {/foreach}
        </select>
    </div>
</div>