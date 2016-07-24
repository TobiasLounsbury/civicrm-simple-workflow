<?php

//TODO: Verify Permissions
//TODO: Work on Force Login

require_once 'CRM/Core/Page.php';
require_once 'workflow.util.php';

class CRM_Workflow_Page_Execute extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle("");
    $wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);
    $workflow = null;

    if ($wid) {

      $workflow = CRM_Workflow_BAO_Workflow::getWorkflow($wid);
      $steps = CRM_Workflow_BAO_Workflow::getWorkflowDetails($wid, false);

      $session = CRM_Core_Session::singleton();
      $userID = $session->get('userID');

      if ($workflow['is_active']) {

        //If this workflow contains a contribution page forward to it
        //And the page injector will take over
        if ($workflow['contains_page'] > 0 &&
          (($workflow['require_login'] && $userID) || !$workflow['require_login']) &&
          (!($steps[1]['entity_table'] == "Profile" && $steps[1]['entity_id'] == $workflow['login_form_id']))) {
          return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/contribute/transact', 'reset=1&id=' . $workflow['contains_page']));
        }

        //Let the page know the method we are using
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('method' => "template")));

        //Add the needed resources to execute the workflow
        simpleWorkflowAddResources("CRM_Workflow_Page_Execute", $this);

        simpleWorkflowPreprocessStepsForExecution($steps);

        //Assign the Data
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array(
          'steps' => $steps,
          'workflow' => $workflow
        )));



        //Set the width for the step lis
        $stepWidth = (empty($steps)) ? 98 : round(98 / sizeof($steps), 0, PHP_ROUND_HALF_DOWN);
        CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('breadcrumWidth' => $stepWidth)));

        //Assign the data so smarty can use it
        $this->assign('workflow', $workflow);
        $this->assign('steps', $steps);
      } else {
        $this->assign('error', "I'm sorry, this workflow has been disabled");
      }
    }
    parent::run();
  }
}
