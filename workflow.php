<?php

require_once 'workflow.civix.php';
require_once 'workflow.hook.php';
require_once 'workflow.util.php';

//$userID = CRM_Core_Session::getLoggedInContactID();

//Hijack the page content so we can add the notifcation container
function workflow_civicrm_alterContent(  &$content, $context, $tplName, &$object ) {
  if ($context == "form" && get_class($object) == "CRM_Contribute_Form_Contribution_Main") {
    if (array_key_exists("workflow", $_GET) && $_GET['workflow'] == 0) {
      return;
    }
    $allPages = CRM_Workflow_BAO_Workflow::getWorkflowPages(true);
    if (array_key_exists($object->_id, $allPages)) {
      $sm = CRM_Core_Smarty::singleton();
      $pos = strrpos($content, "</div>");
      if($pos !== false) {
        $content = substr_replace($content, $sm->fetch("CRM/common/notifications.tpl")."</div>", $pos, strlen("</div>"));
      }
    }
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * @param $formName string, the name of the form
 * @param $form object, a reference to the form object
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function workflow_civicrm_buildForm($formName, &$form) {
  if ($formName == "CRM_Contribute_Form_Contribution_Main") {
    if (array_key_exists("workflow", $_GET) && $_GET['workflow'] == 0) {
      return;
    }

    $allPages = CRM_Workflow_BAO_Workflow::getWorkflowPages(true);
    if (array_key_exists($form->_id, $allPages)) {



      //We have a page to inject into so Load up the workflow
      $workflow = CRM_Workflow_BAO_Workflow::getWorkflow($allPages[$form->_id]);
      if ($workflow['is_active']) {
        $steps = CRM_Workflow_BAO_Workflow::getWorkflowDetails($allPages[$form->_id], false);


        //If we require login forward to workflow page and it will forward it back here.
        if ($workflow['require_login']) {
          //If the login form selected is also the first one in the list we can just load the page.
          if ($steps[1]['entity_table'] == "Profile" && $steps[1]['entity_id'] == $workflow['login_form_id']) {
            if ($form->_contactID) {
              unset($steps[1]);
            }
          } else {
            if (!$form->contactID) {
              return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/workflow', 'reset=1&wid=' . $allPages[$form->_id]['workflow_id']));
            }
          }
        }

        //Let the page know the method we are using
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('method' => "inject")));

        //Inject the needed resources into the page
        simpleWorkflowAddResources($formName, $form, $workflow);

        //Do some pre-processing for the individual steps (delegates to each type)
        simpleWorkflowPreprocessStepsForExecution($steps);

        //Add the settings we need.
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array(
          'steps' => $steps,
          'workflow' => $workflow
        )));

        //Set the width for the step
        //todo: This should be handled client side. We have the number of steps.
        $stepWidth = (empty($steps)) ? 98 : round(98 / sizeof($steps), 0, PHP_ROUND_HALF_DOWN);
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('breadcrumWidth' => $stepWidth)));



        $jquerySteps = array();
        $lastStep = 1;
        foreach($steps as $step) {
          if ($step['entity_table'] == "jquery") {
            $jquerySteps[] = $step['entity_id'];
            $lastStep = $step['order'];
          }
        }

        $jqueryTotal = ($jquerySteps) ? implode(",", $jquerySteps) : false;
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('allSelector' => $jqueryTotal)));
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('lastStep' => $lastStep)));

        //If we are returning to the form.
        if (!empty($form->_submitValues)) {
          CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('returning' => true)));
        }
      }
    }
  }
}


function workflow_workflow_test($params) {
  error_log($params);
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function workflow_civicrm_config(&$config) {
  _workflow_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function workflow_civicrm_xmlMenu(&$files) {
  _workflow_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function workflow_civicrm_install() {
  return _workflow_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function workflow_civicrm_uninstall() {
  return _workflow_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function workflow_civicrm_enable() {
  return _workflow_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function workflow_civicrm_disable() {
  return _workflow_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function workflow_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _workflow_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function workflow_civicrm_managed(&$entities) {
  return _workflow_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function workflow_civicrm_caseTypes(&$caseTypes) {
  _workflow_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function workflow_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _workflow_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Add navigation for Workflowa under "Administer" menu
 *
 * @param $params associated array of navigation menus
 *
 */
function workflow_civicrm_navigationMenu( &$params ) {
  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');

  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under adminster menu
    $maxKey = max( array_keys($params[$administerMenuId]['child']));
    $params[$administerMenuId]['child'][$maxKey+1] =  array (
      'attributes' => array (
        'label'      => 'CiviWorkflows',
        'name'       => 'CiviWorkflows',
        'url'        => 'civicrm/workflows?reset=1',
        'permission' => 'administer CiviCRM',
        'operator'   => NULL,
        'separator'  => false,
        'parentID'   => $administerMenuId,
        'navID'      => $maxKey+1,
        'active'     => 1
      )
    );
  }
}


/**
 * Implements hook_civicrm_postProcess().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function workflow_civicrm_postProcess($formName, &$form) {
  $workflowStep = CRM_Utils_Array::value("SimpleWorkflowFormStep", $form->_submitValues, false);
  if ($workflowStep) {
    list($wid, $stepName) = explode("_", $workflowStep, 2);
    $step = CRM_Workflow_BAO_WorkflowDetail::getDetail($wid, $stepName);
    switch($step['entity_table']) {
      case "Profile":
        $key = "workflow_{$wid}_step_{$step['order']}_contact_id";
        $contactID = $form->getVar("_id");
        //Save the contact_id for this profile
        CRM_Core_Session::singleton()->set($key, $contactID, "SimpleWorkflow");
        break;

      case "Case":
        $caseId = $form->case_id;
        $key = "workflow_{$wid}_step_{$step['order']}_data";
        CRM_Core_Session::singleton()->set($key, $caseId, "SimpleWorkflow");

        $clientId = $form->client_id;
        $key = "workflow_{$wid}_step_{$step['order']}_contact_id";
        CRM_Core_Session::singleton()->set($key, $clientId, "SimpleWorkflow");

        $relationships = CRM_Utils_Array::value("relationships", $step['options'], false);
        if($relationships) {
          _workflow_case_process_relationships($caseId, $clientId, $relationships, $wid);
        }
        break;
      default:
        //Should we do something extra here?
        //Or is it sufficient to allow a third party extension
        //hook into the main civicrm_postProcess hook?
    }

    //Process any extra step details that need to happen.
    CRM_Workflow_hook::completeStep($wid, $step['name'], $form);
  }
}


function workflow_workflow_complete_step($wid, $stepName, &$context) {
  $step = CRM_Workflow_BAO_WorkflowDetail::getDetail($wid, $stepName);

  switch($step['entity_table']) {
    case "Profile":
      //Process the relationships for this profile if there are any
      $relationships = CRM_Utils_Array::value("relationships", $step['options'], false);
      if($relationships) {
        $key = "workflow_{$wid}_step_{$step['order']}_contact_id";
        $contactID = CRM_Core_Session::singleton()->get($key, "SimpleWorkflow");
        _workflow_profile_process_relationships($contactID, $relationships, $wid);
      }
      break;
    case "Case":

      break;
  }

}

/**
 * Implementation of hook_workflow_getStepParams
 */
function workflow_workflow_getStepParams(&$step, $workflowId) {
  switch($step['entity_table']) {
    case "Profile":
      simpleWorkflowPreprocessProfileStep($step, $workflowId);
      break;
    default:
  }
}

/**
 * Implements hook_civicrm_alterAPIPermissions
 */
function workflow_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['workflow']['complete_step'] = array('access ajax api');
}
