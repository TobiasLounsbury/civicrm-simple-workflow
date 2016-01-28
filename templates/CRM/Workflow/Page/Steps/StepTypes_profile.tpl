<div id="SimpleWorkflowProfileTypeTemplate" class="profile Detail">
    <span class='handle'>ↈ</span>
    <span class='DeleteProfile' onclick='DeleteStep(this)'>delete</span>
    <input type="hidden" name="data[#ORDER#][order]" class="order"/>
    <input type="hidden" name="data[#ORDER#][entity_table]" />
    <div class="crm-simple-workflow-step-details">
        <input type="hidden" name="data[#ORDER#][entity_id]" />
        <div class='entity_name'></div>

        <label class="leftmost">Breadcrumb:</label>
        <input name="data[#ORDER#][breadcrumb]" />

        <label>Button Text:</label>
        <input name="data[#ORDER#][next]" />

        <label>Title:</label>
        <input name="data[#ORDER#][title]" size='40' />
    </div>
</div>