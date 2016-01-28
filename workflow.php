<?php

require_once 'workflow.civix.php';
require_once 'workflow.hook.php';

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

                //Inject the needed resources into the page

                //Let the page know we are injecting into a page rather than loading a template.
                CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('method' => "inject")));

                //Add Stylesheet
                CRM_Core_Resources::singleton()->addStyleFile('org.botany.workflow', 'workflow_execute.css');


                //Add JS Libraries if we need them for notifications.
                if (!CRM_Core_Permission::check('access CiviCRM')) {
                    $notiFile = CRM_Core_Resources::singleton()->getUrl('civicrm', "packages/jquery/plugins/jquery.notify.min.js", true);
                    CRM_Core_Resources::singleton()->addScriptUrl($notiFile, -2, 'html-header');

                    $blockFile = CRM_Core_Resources::singleton()->getUrl('civicrm', "packages/jquery/plugins/jquery.blockUI.min.js", true);
                    CRM_Core_Resources::singleton()->addScriptUrl($blockFile, -1, 'html-header');
                }

                //Add Javascript files and settings
                CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'workflow_execute.js', 20, 'page-footer');
                CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('steps' => $steps)));

                //Set the width for the step lis
                $stepWidth = (empty($steps)) ? 98 : round(98 / sizeof($steps), 0, PHP_ROUND_HALF_DOWN);
                CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('breadcrumWidth' => $stepWidth)));

                $jquerySteps = "";
                $lastStep = 1;
                foreach($steps as $step) {
                    if ($step['entity_table'] == "jQuery") {
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
