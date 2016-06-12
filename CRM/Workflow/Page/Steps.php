<?php


//TODO: Allow custom javascript function per step
//TODO: Create some logic in the interface to allow selection of elements based on price-sets and profiles
//TODO: With new interface, continue to allow custom selectors
//TODO: cross browser check


require_once 'CRM/Core/Page.php';

class CRM_Workflow_Page_Steps extends CRM_Core_Page {
  function run() {
    $wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);

    if ($wid === false) {
      throw new CRM_Exception(ts("That Workflow couldn't be found."));
    }

    $ccr = CRM_Core_Resources::singleton();

    //Fetch Case Types
    $results = civicrm_api3('CaseType', 'get');
    $caseTypes = ($results['is_error'] == 0) ? $results['values'] : array();

    //todo: Clean this up so SQL isn't scattered around
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

      if($dao->options) {
        $details[$dao->order]['options'] = json_decode($dao->options);
      }

      if($dao->entity_table == "Case") {
        $details[$dao->order]['entity_name'] = $caseTypes[$dao->entity_id]['title'];
      }

      if($dao->entity_table == "Profile") {
        $details[$dao->order]['entity_name'] = civicrm_api3('UFGroup', 'getvalue', array(
          'return' => "title",
          'id' => $dao->entity_id,
        ));
      }

      if($dao->entity_table == "Page") {
        $details[$dao->order]['entity_name'] = $result = civicrm_api3('ContributionPage', 'getvalue', array(
          'return' => "title",
          'id' => $dao->entity_id,
        ));
      }
    }

    CRM_Utils_System::setTitle(ts("Details for ".$workflow['name'].":"));

    $result = civicrm_api3('ContributionPage', 'get', array(
      'sequential' => 1,
      'return' => array("id", "title"),
    ));
    $pages = $result['values'];
    $this->assign("pages", $pages);

    $this->assign('workflow', $workflow);
    $this->assign('details', $details);


    //Add Stylesheet
    $ccr->addStyleFile('org.botany.workflow', 'css/workflow_steps.css');
    //Add JavaScript
    $ccr->addScriptFile('org.botany.workflow', 'js/workflow_steps.js', 100);
    $ccr->addScriptFile('org.botany.workflow', 'js/jquery.serialize-object.min.js', -200, 'html-header');
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
      "CRM/Workflow/Page/Steps/AddStep_case.tpl",
      "CRM/Workflow/Page/Steps/AddStep_html.tpl",
    );
    $typeTemplates = array(
      "CRM/Workflow/Page/Steps/StepTypes_profile.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_page.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_jquery.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_url.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_case.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_html.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_default.tpl",
      "CRM/Workflow/Page/Steps/StepTypes_relationship_template.tpl"
    );

    $javaScript = array(
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_url.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_profile.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_page.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_jquery.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_case.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_widget_relationships.js'),
      $ccr->getUrl('org.botany.workflow', 'js/workflow_steps_html.js')
    );
    $css = array();

    CRM_Workflow_hook::getStepTypes($uiTemplates, $typeTemplates, $javaScript, $css);

    foreach($css as $file) {
      $ccr->addStyleUrl($file);
    }

    foreach($javaScript as $file) {
      $ccr->addScriptUrl($file, 50);
    }

    $this->assign('uiTemplates', $uiTemplates);
    $this->assign('typeTemplates', $typeTemplates);


    //Add the wysiwyg libraries for < 4.7
    $version = substr(CRM_Utils_System::version(), 0, 3);
    if($version <= 4.6) {
      //crmUi depends on loading ckeditor, but ckeditor doesn't work with aggregation.
      $ccr->addScriptFile('civicrm', 'packages/ckeditor/ckeditor.js', 120, 'html-header', FALSE);
    }


    //Support for Profiles
    $entities = array(array('entity_name' => 'contact_1', 'entity_type' => 'IndividualModel'));
    $allowCoreTypes = array_merge(array('Contact', 'Individual', 'Case', "Organization", "Household"), CRM_Contact_BAO_ContactType::subTypes('Individual'));
    $allowCoreTypes = array_merge($allowCoreTypes, CRM_Contact_BAO_ContactType::subTypes('Organization'));
    CRM_UF_Page_ProfileEditor::registerProfileScripts();
    CRM_UF_Page_ProfileEditor::registerSchemas(CRM_Utils_Array::collect('entity_type', $entities));
    $this->assign('profilesDataGroupType', CRM_Core_BAO_UFGroup::encodeGroupType($allowCoreTypes, array(), ';;'));
    $this->assign('profilesDataEntities', json_encode($entities));

    //Support for Relationships
    $result = civicrm_api3('RelationshipType', 'get', array(
      'is_active' => 1,
    ));
    $relTypes = array();
    $relTypeOptions = array();
    if ($result['count'] > 0) {
      foreach ($result['values'] as $relType) {
        $relTypes[$relType['id']] = $relType;
        $relTypeOptions[$relType['id']. '_a_b'] = $relType['label_a_b'];
        if ($relType['label_a_b'] != $relType['label_b_a']) {
          $relTypeOptions[$relType['id']. '_b_a'] = $relType['label_b_a'];
        }
      }
    }

    //Assign relationship data
    $this->assign('relationshipTypeOptions', $relTypeOptions);
    $this->assign("relationshipWidget", "CRM/Workflow/Page/Steps/StepTypes_relationship_widget.tpl");
    $ccr->addVars('SimpleWorkflow', array(
      "relationshipTypeOptions" => $relTypeOptions,
      "relationshipTypes" => $relTypes
    ));


    //Add the Case Data to page
    $this->assign('caseTypes', $caseTypes);
    $ccr->addVars('SimpleWorkflow', array(
      "caseTypes" => $caseTypes,
    ));

    $mediums = civicrm_api3('Activity', 'getoptions', array(
      'field' => "activity_medium_id",
    ));
    $this->assign('caseMediums', $mediums['values']);

    $this->assign('caseStatus', CRM_Case_PseudoConstant::caseStatus());

    parent::run();
  }



}
