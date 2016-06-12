<div id="SimpleWorkflowTypeTemplateProfile">
    <p>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="edit" id="data_#ORDER#_options_mode_update" /> <label for="data_#ORDER#_options_mode_update">{ts}Use to update current Contact{/ts}</label>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="create" id="data_#ORDER#_options_mode_create" /> <label for="data_#ORDER#_options_mode_create">{ts}Use to create new Contact{/ts}</label>
    </p>
    <!--// Add relationships that will be auto-created if this is a new contact //-->
    {include file=$relationshipWidget location="bottom"}
    <input type="hidden" name="data[#ORDER#][entity_id]" class="entity_id" />
</div>