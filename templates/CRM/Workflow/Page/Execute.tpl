{if $error}
    <div class="error">{$error}</div>
{else}
    <div id="SW_PreFormMessage">{$workflow.pre_message}</div>
    <ol class="WorkflowSteps" id="WorkflowSteps"><!--
    {foreach from=$steps item=step}
        --><li class="stepTodo {$step.entity_table}" data-order="{$step.order}"><span>{$step.breadcrumb}</span></li><!--
    {/foreach}
--></ol>
    <div id="PreMessage"></div>
    <div id="ActionWindow"></div>
    <div id="PostMessage"></div>
    <a href='#' id='SWNextButton' class='button'><span> Next </span></a><div class='clear'></div>
    <div id="SW_PostFormMessage">{$workflow.post_message}</div>
{/if}

{include file="CRM/common/notifications.tpl" location="bottom"}