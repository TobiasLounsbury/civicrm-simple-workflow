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


    //This causes the wysiwyg libraries to be included on the page.
    $form->assign('includeWysiwygEditor', true);
  }