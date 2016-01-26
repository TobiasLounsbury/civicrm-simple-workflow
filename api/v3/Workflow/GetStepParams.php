<?php


function _civicrm_api3_workflow_get_step_params_spec(&$spec) {
  $spec['step']['api.required'] = 1;
  $spec['workflow_id']['api.required'] = 1;
  $spec['workflow_id']['api.aliases'] = array('wid', 'workflow');
}

function civicrm_api3_workflow_get_step_params($params) {
  return civicrm_api3_create_success("Success", $params, 'Workflow', 'getStepParams');
}