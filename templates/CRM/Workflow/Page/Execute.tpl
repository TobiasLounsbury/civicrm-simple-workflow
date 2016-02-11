{if $error}
    <div class="error">{$error}</div>
{else}
<ol class="WorkflowSteps" id="WorkflowSteps"><!--
    {foreach from=$steps item=step}
        --><li class="stepTodo {$step.entity_table}" data-order="{$step.order}"><span>{$step.breadcrumb}</span></li><!--
    {/foreach}
--></ol>
<div id="PreMessage"></div>
<div id="ActionWindow"></div>
<div id="PostMessage"></div>
{/if}

{include file="CRM/common/notifications.tpl" location="bottom"}