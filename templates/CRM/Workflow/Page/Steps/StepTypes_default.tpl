<div id="SimpleWorkflowTypeTemplateDefault" class="Detail">
    <span class='handle'>ↈ</span>
    <span class='DeleteStep'>{ts}delete{/ts}</span>
    <input type="hidden" name="data[#ORDER#][order]" class="order"/>
    <input type="hidden" name="data[#ORDER#][entity_table]" class="entity_table" />

    <div class='entity_name'></div>

    <div class="SWStepInlineField">
        <label>{ts}Breadcrumb{/ts}:</label>
        <input name="data[#ORDER#][breadcrumb]" class="step_breadcrumb" />
    </div>

    <div class="SWStepInlineField">
        <label>{ts}Name{/ts}:</label>
        <input name="data[#ORDER#][name]" class="step_name" />
    </div>

    <hr />
    <span class="crm-button SWToggleDetails">{ts}Show Details{/ts}</span>
    <br />
    <div class="crm-simple-workflow-step-details">
        <div class="SWStepField">
            <label>{ts}Button Text{/ts}:</label>
            <input name="data[#ORDER#][next]" class="step_next" />
        </div>
        <div class="SWStepField">
            <label>{ts}Title{/ts}:</label>
            <input name="data[#ORDER#][title]" size='40' class="step_title" />
        </div>
        <div class="SWStepField">
            <label>{ts}Pre-Step HTML{/ts}:</label><br />
            <textarea name="data[#ORDER#][pre_message]" class="pre_message"></textarea>
        </div>
        <div class="SWStepField">
            <label>{ts}Post-Step HTML{/ts}:</label><br />
            <textarea name="data[#ORDER#][post_message]" class="post_message"></textarea>
        </div>
    </div>
    <div class="clear"></div>
</div>