<div id="SimpleWorkflowTypeTemplateProfile">
    <p>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="edit" id="data_#ORDER#_options_mode_update" /> <label for="data_#ORDER#_options_mode_update">{ts}Use to update current Contact{/ts}</label>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="create" id="data_#ORDER#_options_mode_create" /> <label for="data_#ORDER#_options_mode_create">{ts}Use to create new Contact{/ts}</label>
        <input class="SW-Profile-Mode" type="radio" name="data[#ORDER#][options][mode]" value="select" id="data_#ORDER#_options_mode_select" /> <label for="data_#ORDER#_options_mode_select">{ts}Select existing or create new Contact{/ts}</label>
    </p>

    <fieldset class="SW-Profile-Select-Existing-Wrapper">
        <legend>{ts}Existing Contact Filters{/ts}</legend>
        <p>Limit Contact Search to:</p>

        <div class="SWStepField">
            <label>{ts}Group{/ts}:</label>
            <select name="data[#ORDER#][options][existingGroup]" class="profile_option_existing_group">
                {foreach from=$allGroups item=group}
                    <option value="{$group.id}">{$group.title}</option>
                {/foreach}
            </select>
        </div>

        <div class="SWStepField">
            <label>{ts}Field Label{/ts}:</label>
            <input name="data[#ORDER#][options][existingFieldLabel]" class="profile_option_existing_field_label" />
        </div>

        <div class="SWStepField">
            <label>{ts}Button Text{/ts}:</label>
            <input name="data[#ORDER#][options][existingButtonText]" class="profile_option_existing_button_text" />
        </div>

        <div class="SWStepField">
            <label>{ts}"Or Message"{/ts}:</label>
            <input name="data[#ORDER#][options][existingOrMessage]" class="profile_option_existing_or_message" />
        </div>

        <!--//
        <div class="SWStepField">
            <label>{ts}Contact Type{/ts}:</label>
            <select name="data[#ORDER#][options][existingType]" class="profile_option_existing_type">
                <option value="">{ts}All Contacts{/ts}</option>
                {foreach from=$contactTypes item=cGroup key=cGroupName}
                    <optgroup label="{$cGroup.label}">
                        <option value="{$cGroupName}">{ts}All{/ts} {$cGroup.label}</option>
                        {foreach from=$cGroup.subTypes item=ctTitle key=ctName}
                            <option value="{$ctName}">{$ctTitle}</option>
                        {/foreach}

                    </optgroup>
                {/foreach}
            </select>
        </div>
        //-->
    </fieldset>

    <!--// Add relationships that will be auto-created if this is a new contact //-->
    <fieldset class="SW-Profile-Relationships-Wrapper">
        <legend>{ts}Relationships{/ts}</legend>
        <p>{ts key='profile-relationship-help'}Relationships that will be created for this contact:{/ts}</p>
        {include file=$relationshipWidget location="bottom"}
    </fieldset>

    <input type="hidden" name="data[#ORDER#][entity_id]" class="entity_id" />
</div>