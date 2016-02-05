<div id="SimpleWorkflowTypeTemplateCase">
    <input type ="hidden" name="data[#ORDER#][entity_id]" class="entity_id" />

    <div class="SWStepField">
        <input type ="checkbox" name="data[#ORDER#][options][include_profile]" class="case_option_include_profile" />
        <label>{ts}Include Profile/Custom Data{/ts}</label>
    </div>

    <div class="SWStepField">
        <label>{ts}Mode{/ts}:</label>
        <select name="data[#ORDER#][options][mode]" class="case_option_mode">
            <option value="create">{ts}Create{/ts}</option>
            <option value="edit">{ts}Edit{/ts}</option>
        </select>
    </div>

    <div class="SWStepField">
        <label>{ts}Fields to Include{/ts}:</label>
        <input name="data[#ORDER#][options][core_fields]" class="case_option_core_fields" />
    </div>

</div>