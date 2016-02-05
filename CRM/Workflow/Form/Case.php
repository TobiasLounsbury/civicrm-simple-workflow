<?php

require_once 'CRM/Core/Form.php';

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
    $this->_name = CRM_Utils_Request::retrieve('name', 'String', $this, false, 0);

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

    //todo: Add core fields


    //Todo: Add included profile


    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();


    parent::postProcess();
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
