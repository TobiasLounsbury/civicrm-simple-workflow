<?php

require_once 'CRM/Core/Form.php';
require_once 'workflow.util.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Workflow_Form_Case_Activity extends CRM_Core_Form {

  protected $_wid;
  protected $_name;
  protected $_workflow;
  protected $_step;
  protected $_case;
  protected $_activity;
  protected $_profiles;

  /**
   * Function to set variables up before form is built
   *
   * @param null
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    // current set id
    $this->_wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);
    $this->_name = CRM_Utils_Request::retrieve('stepName', 'String', $this, false, 0);

    $this->_workflow = CRM_Workflow_BAO_Workflow::getWorkflow($this->_wid);
    $this->_step = CRM_Workflow_BAO_WorkflowDetail::getDetail($this->_wid, $this->_name);

    $key = "workflow_{$this->_wid}_step_{$this->_step['options']['case_order']}_data";
    $caseId = CRM_Core_Session::singleton()->get($key, "SimpleWorkflow");

    $key = "workflow_{$this->_wid}_step_{$this->_step['options']['case_order']}_contact_id";
    $contactId = CRM_Core_Session::singleton()->get($key, "SimpleWorkflow");

    $params = array("activity_type_id" => $this->_step['entity_id']);
    $activities = CRM_Case_BAO_Case::getCaseActivity($caseId, $params, $contactId);
    $activityId = $activities['data'][0]['DT_RowId'];

    $res = civicrm_api3("Activity", "get", array("id" => $activityId, "sequential" => 1));

    $this->_activity = $res['values'][0];

    parent::preProcess();
  }

  function buildQuickForm() {

    //CRM_Utils_System::setTitle($title);

    //Add included profile/custom fields
    if(array_key_exists("options", $this->_step) &&
      array_key_exists("include_profile", $this->_step['options']) &&
      $this->_step['options']['include_profile']) {

      //Add the profile to the form
      $contactID = CRM_Utils_Array::value('userID', $_SESSION['CiviCRM']);

      $pids = $this->_step['options']['include_profile'];
      if(!is_array($pids)) {
        $pids = explode(",", $pids);
      }

      $this->_profiles = $this->buildCustom($pids, $contactID);
      $this->assign('customProfiles', $this->_profiles);

      $this->addButtons(array(
        array(
          'type' => 'submit',
          'name' => ts('Submit'),
          'isDefault' => TRUE,
        ),
      ));
    }

    parent::buildQuickForm();
  }



  public function setDefaultValues() {
    $defaults = array();
    return $defaults;
  }

  function postProcess() {
    $values = $this->exportValues();

    $params = $this->_activity;

    //Cleanup Data before we call create otherwise the custom_[x]
    //values that are blank cause errors.
    //We aren't actually storing these values here, because of bugs in the Case API.
    foreach($this->_profiles as $fields) {
      foreach($fields as $fieldName => $field) {
        if(array_key_exists("html_type", $field) && $field['html_type'] == "CheckBox") {
          $value = $values[$fieldName];
          if (is_array($value)) {
            $value = array_filter($value);
            $value = array_keys($value);
          }
          $values[$fieldName] = $value;
        }
        $params[$fieldName] = $values[$fieldName];
      }
    }

    $params['status_id'] = $this->_step['options']['status'];
    $activity = civicrm_api3('Activity', 'create', $params);

    parent::postProcess();

    CRM_Workflow_hook::completeStep($this->_wid, $this->_name, $this);
  }


  function buildCustom(array $profileIds = array(), $contactID = null, $prefix = '') {
    $profiles = array();
    $fieldList = array(); // master field list

    foreach($profileIds as $profileID) {
      $fields = CRM_Core_BAO_UFGroup::getFields($profileID, FALSE, CRM_Core_Action::ADD,
        NULL, NULL, FALSE, NULL,
        FALSE, NULL, CRM_Core_Permission::CREATE,
        'field_name', TRUE
      );

      foreach ($fields as $key => $field) {
        if (array_key_exists($key, $fieldList)) continue;

        CRM_Core_BAO_UFGroup::buildProfile(
          $this,
          $field,
          CRM_Profile_Form::MODE_CREATE,
          $contactID,
          TRUE,
          null,
          null,
          $prefix
        );
        $profiles[$profileID][$key] = $fieldList[$key] = $field;
      }
    }
    return $profiles;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
