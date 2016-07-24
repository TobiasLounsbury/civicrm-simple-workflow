<?php


function _civicrm_api3_workflow_complete_step_spec(&$spec) {
  $spec['step']['api.required'] = 1;
  $spec['workflow_id']['api.required'] = 1;
  $spec['workflow_id']['api.aliases'] = array('wid', 'workflow');
}

function civicrm_api3_workflow_complete_step($params) {


  if(array_key_exists("contact", $params)) {
    $key = "workflow_{$params['workflow_id']}_step_{$params['step']}_contact_id";
    CRM_Core_Session::singleton()->set($key, $params['contact'], "SimpleWorkflow");
  }

  if(array_key_exists("data", $params)) {
    $key = "workflow_{$params['workflow_id']}_step_{$params['step']}_data";
    CRM_Core_Session::singleton()->set($key, $params['data'], "SimpleWorkflow");
  }


  return civicrm_api3_create_success("Success", $params, 'Workflow', 'completeStep');
}