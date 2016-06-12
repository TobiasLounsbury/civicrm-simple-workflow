<div class="SW-Relationship-Template">
    <select class="SW-Relationship-Type" name="data[#ORDER#][options][relationships][#RELS#][relType]">
        {foreach from=$relationshipTypeOptions item=typeLabel key=typeId}
            <option value="{$typeId}">{$typeLabel}</option>
        {/foreach}
    </select>
    <input type="hidden" class="SW-Relationship-Contact-Hidden" name="data[#ORDER#][options][relationships][#RELS#][contact]" />
    <select class="SW-Relationship-Contact">
        <option value="<user>">Active User</option>
    </select>
    <input type="button" value="x" class="SW-Relationship-Remove-Button" />
</div>