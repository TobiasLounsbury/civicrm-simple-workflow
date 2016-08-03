<?php

require_once 'CRM/Core/Form.php';
require_once 'workflow.util.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Workflow_Form_Case extends CRM_Core_Form {

  protected $_wid;
  protected $_name;
  protected $_workflow;
  protected $_step;
  protected $_caseType;
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

    $result = civicrm_api3('CaseType', 'get', array(
      'sequential' => 1,
      'id' => $this->_step['entity_id'],
      'return' => "all"
    ));

    $this->_caseType = $result['values'][0];

    parent::preProcess();
  }

  function buildQuickForm() {


    //CRM_Utils_System::setTitle($title);

    //Parse the list of fields
    $fields = array();
    if(array_key_exists("options", $this->_step) && array_key_exists("core_fields", $this->_step['options'])) {
      $fields = explode(",", $this->_step['options']['core_fields']);
    }

    //Client
    if (in_array("client_id", $fields)) {
      $this->addEntityRef('client_id', ts('Client'), array(
        'create' => TRUE,
        'multiple' => $this->_allowMultiClient,
      ), TRUE);
    }

    //Activity Medium
    if (in_array("medium_id", $fields)) {
      $this->addSelect('medium_id', array('entity' => 'activity'), TRUE);
    }

    //Activity Medium
    if (in_array("activity_location", $fields)) {
      $this->add('text', 'activity_location', ts('Location'), CRM_Core_DAO::getAttribute('CRM_Activity_DAO_Activity', 'location'));
    }

    //Details
    if (in_array("activity_details", $fields)) {
      $this->add('wysiwyg', 'activity_details', ts('Details'), array('rows' => 4, 'cols' => 60), FALSE);
    }

    //Subject
    if (in_array("activity_subject", $fields)) {
      $s = CRM_Core_DAO::getAttribute('CRM_Activity_DAO_Activity', 'subject');
      if (!is_array($s)) {
        $s = array();
      }

      $this->add('text', 'activity_subject', ts('Subject'),
        array_merge($s, array(
          'maxlength' => '128',
        )), TRUE
      );
    }

    //Case Type
    if (in_array("case_type_id", $fields)) {
      $caseTypes = CRM_Case_PseudoConstant::caseType();
      $element = $this->add('select',
        'case_type_id', ts('Case Type'), $caseTypes,
        TRUE, array('onchange' => "CRM.buildCustomData('Case', this.value);")
      );

      if ($this->_caseTypeId) {
        $element->freeze();
      }
    }

    //Case Status
    if (in_array("status_id", $fields)) {
      $csElement = $this->add('select', 'status_id', ts('Case Status'),
        CRM_Case_PseudoConstant::caseStatus(),
        FALSE
      );
    }

    //Case Start Date
    if (in_array("start_date", $fields)) {
      $this->addDate('start_date', ts('Case Start Date'), TRUE, array('formatType' => 'activityDateTime'));
    }

    //Activity Duration
    if (in_array("duration", $fields)) {
      $this->add('text', 'duration', ts('Activity Duration'), array('size' => 4, 'maxlength' => 8));
      $this->addRule('duration', ts('Please enter the duration as number of minutes (integers only).'), 'positiveInteger');
    }

    //todo: Attachments
    if (in_array("attachments", $fields)) {

      $this->assign("action", 1);
      $this->assign("includeAttachments", true);
    }


    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());


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
    }

    parent::buildQuickForm();
  }



  public function setDefaultValues() {
    $defaults = array();

    if(array_key_exists("options", $this->_step) && array_key_exists("defaults", $this->_step['options'])) {
      $defaults = $this->_step['options']['defaults'];
      $defaults['client_id'] = _workflow_get_step_contact($this->_wid, $defaults['client_id']);
    }

    return $defaults;
  }

  function postProcess() {
    $values = $this->exportValues();
    $defaults = array();

    if(array_key_exists("options", $this->_step) && array_key_exists("defaults", $this->_step['options'])) {
      $defaults = $this->_step['options']['defaults'];
    }

    $defaults['client_id'] = _workflow_get_step_contact($this->_wid, $defaults['client_id']);

    $values = array_merge($defaults, $values);

    $values['contact_id'] = $values['client_id'];
    unset($values['client_id']);

    //Set the case type
    $values['case_type_id'] = $this->_step['entity_id'];

    if(!array_key_exists("subject", $values)) {
      if(array_key_exists("case_subject", $values)) {
        $values['subject'] = $values['case_subject'];
      } else {
        $values['subject'] = $values['activity_subject'];
      }
    }


    $fields = null;
    $entityID = null;
    //This is the peculiar format that Case requires for custom data
    $values['custom'] = CRM_Core_BAO_CustomField::postProcess(
      $values,
      $fields,
      'Case',
      false, // inline
      false //Check perms
    );

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
      }
    }

    //There must be a creatorId associated with a case.
    //If the user is anonymous default to the clientContactId
    $currentUserId = CRM_Core_Session::getLoggedInContactID();
    if (!$currentUserId) {
      $values['creator_id'] = $values['contact_id'];
    }

    $case = civicrm_api3('Case', 'create', $values);

    //This is a hack to store custom case data because the API explicitly
    //strips out all custom data passed to the API ad replaces it with an empty array
    if($case['is_error'] == 0 && array_key_exists("id", $case)) {
      $this->case = $case['values'][$case['id']];
      $this->case_id = $case['id'];
      $this->client_id = $values['contact_id'];
      CRM_Core_BAO_CustomValueTable::store($values['custom'], 'civicrm_case', $case['id']);
    }

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
