<div id="SimpleWorkflowTypeTemplateProfile">
    <p>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="edit" id="data_#ORDER#_options_mode_update" /> <label for="data_#ORDER#_options_mode_update">{ts}Use to update current Contact{/ts}</label>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="create" id="data_#ORDER#_options_mode_create" /> <label for="data_#ORDER#_options_mode_create">{ts}Use to create new Contact{/ts}</label>
    </p>

    <!--// Add relationships that will be auto-created if this is a new contact //-->
    <div class="SW-Profile-Relationships-Wrapper SWStepField">
        <label>{ts}Relationships{/ts}</label>
        <p>{ts key='profile-relationship-help'}Relationships that will be created for this contact:{/ts}</p>
        {include file=$relationshipWidget location="bottom"}
    </div>

    <input type="hidden" name="data[#ORDER#][entity_id]" class="entity_id" />
</div>