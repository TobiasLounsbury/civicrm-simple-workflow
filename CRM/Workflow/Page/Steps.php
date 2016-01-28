<?php


//TODO: Allow custom javascript function per step
//TODO: Create some logic in the interface to allow selection of elements based on price-sets and profiles
//TODO: With new interface, continue to allow custom selectors
//TODO: cross browser check


require_once 'CRM/Core/Page.php';

class CRM_Workflow_Page_Steps extends CRM_Core_Page {
  function run() {
    $wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);

    if ($wid) {
      $workflowTable = CRM_Workflow_DAO_Workflow::$_tableName;
      $detailTable = CRM_Workflow_DAO_WorkflowDetail::$_tableName;
      $wsql = "SELECT * FROM `".$workflowTable."` WHERE id = {$wid} LIMIT 1";
      $dao =& CRM_Core_DAO::executeQuery($wsql);
      if (!$dao->fetch()) {
        $workflow = null;
      } else {
        $workflow = (array) $dao;
      }

      $dsql = "SELECT * FROM `".$detailTable."` WHERE workflow_id = {$wid} ORDER BY `order`";
      $dao =& CRM_Core_DAO::executeQuery($dsql);

      $details = array();

      while ($dao->fetch()) {
        $details[$dao->order] = (array) $dao;

        if($dao->entity_table == "Profile") {
          $result = civicrm_api3('UFGroup', 'get', array(
            'sequential' => 1,
            'return' => "title",
            'id' => $dao->entity_id,
          ));
          $details[$dao->order]['name'] = $result['values'][0]['title'];
        }
        if($dao->entity_table == "Page") {
          $result = civicrm_api3('ContributionPage', 'get', array(
            'sequential' => 1,
            'return' => "title",
            'id' => $dao->entity_id,
          ));
          $details[$dao->order]['name'] = $result['values'][0]['title'];
        }

      }

      CRM_Utils_System::setTitle(ts("Details for ".$workflow['name'].":"));



    } else {
      $workflow = null;
      $details = array();
    }

    $result = civicrm_api3('ContributionPage', 'get', array(
      'sequential' => 1,
      'return' => array("id", "title"),
    ));
    $pages = $result['values'];
    $this->assign("pages", $pages);

    $this->assign('workflow', $workflow);
    $this->assign('details', $details);

    $ccr = CRM_Core_Resources::singleton();
    //Add Stylesheet
    $ccr->addStyleFile('org.botany.workflow', 'css/workflow_steps.css');
    //Add JavaScript
    $ccr->addScriptFile('org.botany.workflow', 'js/workflow_steps.js');
    //Add Settings
    $ccr->addVars('SimpleWorkflow', array(
      "wid" => $wid,
      "workflow" => $workflow,
      "details" => $details,
      "pages" => $pages
    ));


    $uiTemplates = array(
      "CRM/Workflow/Page/Steps/AddStep_profile.tpl",
      "CRM/Workflow/Page/Steps/AddStep_page.tpl",
      "CRM/Workflow/Page/Steps/AddStep_jquery.tpl",
      "CRM/Workflow/Page/Steps/AddStep_url.tpl",
    );
    $typeTemplates = array(
      "CRM/Workflow/Page/Steps/StepTypes_profile.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_page.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_jquery.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_url.tpl"
    );
    
    $javaScript = array(
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_url.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_profile.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_page.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_jquery.js')
    );
    $css = array();

    CRM_Workflow_hook::getStepTypes($uiTemplates, $typeTemplates, $javaScript, $css);

    foreach($css as $file) {
      $ccr->addStyleUrl($file);
    }

    foreach($javaScript as $file) {
      $ccr->addScriptUrl($file);
    }

    $this->assign('uiTemplates', $uiTemplates);
    $this->assign('typeTemplates', $typeTemplates);


    $entities = array(array('entity_name' => 'contact_1', 'entity_type' => 'IndividualModel'));
    $allowCoreTypes = array_merge(array('Contact', 'Individual', 'Case'), CRM_Contact_BAO_ContactType::subTypes('Individual'));
    CRM_UF_Page_ProfileEditor::registerProfileScripts();
    CRM_UF_Page_ProfileEditor::registerSchemas(CRM_Utils_Array::collect('entity_type', $entities));

    $this->assign('profilesDataGroupType', CRM_Core_BAO_UFGroup::encodeGroupType($allowCoreTypes, array(), ';;'));
    $this->assign('profilesDataEntities', json_encode($entities));

    parent::run();
  }



}
