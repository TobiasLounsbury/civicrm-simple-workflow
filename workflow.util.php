<?php

require_once 'workflow.hook.php';

/**
 *  This function injects the needed resources to run a
 * workflow
 *
 */
function simpleWorkflowAddResources($formName, &$form) {

  //Add Stylesheet
  CRM_Core_Resources::singleton()->addStyleFile('org.botany.workflow', 'css/workflow_execute.css');


  //Add JS Libraries if we need them for notifications.
  if (!CRM_Core_Permission::check('access CiviCRM')) {
    $notiFile = CRM_Core_Resources::singleton()->getUrl('civicrm', "packages/jquery/plugins/jquery.notify.min.js", true);
    CRM_Core_Resources::singleton()->addScriptUrl($notiFile, -2, 'html-header');

    $blockFile = CRM_Core_Resources::singleton()->getUrl('civicrm', "packages/jquery/plugins/jquery.blockUI.min.js", true);
    CRM_Core_Resources::singleton()->addScriptUrl($blockFile, -1, 'html-header');
  }

  //Allow other extensions to include files.
  CRM_Workflow_hook::execute($formName, $form);

  //Add Javascript files and settings
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute.js', 100, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_page.js', 21, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_profile.js', 21, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_jquery.js', 21, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_url.js', 21, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_html.js', 21, 'page-footer');
  CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'js/workflow_execute_case.js', 21, 'page-footer');


  //This causes the wysiwyg libraries to be included on the page.
  $form->assign('includeWysiwygEditor', true);
}

function _workflow_get_step_contact($wid, $contact) {
  if ($contact == "<user>") {
    return CRM_Core_Session::getLoggedInContactID();
  } else {
    $key = "workflow_{$wid}_step_{$contact}_contact_id";
    return CRM_Core_Session::singleton()->get($key, "SimpleWorkflow");
  }
}

function _workflow_profile_process_relationships($contactID, $relationships, $wid) {
  foreach($relationships as $relationship) {

    $relatedContact = _workflow_get_step_contact($wid, $relationship->contact);

    if($contactID && $relatedContact) {
      list($type, $primary, $secondary) = explode("_", $relationship->relType);

      $params = array(
        "relationship_type_id" => $type,
        "contact_id_{$primary}" => $contactID,
        "contact_id_{$secondary}" => $relatedContact
      );

      try {
        $result = civicrm_api3("Relationship", "create", $params);
      } catch (Exception $e) {
        CRM_Core_Error::debug_log_message($e->getMessage());
      }
    }
  }
}

function _workflow_case_process_relationships($case, $relationships, $wid) {
  foreach($relationships as $relationship) {
    $relatedContact = _workflow_get_step_contact($wid, $relationship->contact);
    if($case && $relatedContact) {
      list($type, $primary, $secondary) = explode("_", $relationship->relType);

      $params = array(
        "relationship_type_id" => $type,
        "contact_id_{$primary}" => $relatedContact,
        "contact_id_{$secondary}" => $relatedContact,
        "case_id" => $case
      );

      try {
        $result = civicrm_api3("Relationship", "create", $params);
      } catch (Exception $e) {
        CRM_Core_Error::debug_log_message($e->getMessage());
      }
    }
  }
}