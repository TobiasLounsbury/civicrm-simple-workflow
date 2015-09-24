<?php

//TODO: Allow custom injection jquery selector For where to place Breadcrumbs
//TODO: Allow custom injection jquery selector For where to place ActionWindow
//TODO: Allow custom injection jquery selector For where to move intro text

require_once 'CRM/Core/Form.php';
require_once 'CRM/Workflow/BAO/Workflow.php';


/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Workflow_Form_Workflow extends CRM_Core_Form {

    /**
     * the set id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_wid;

    /**
     * Function to set variables up before form is built
     *
     * @param null
     *
     * @return void
     * @access public
     */
    public function preProcess() {

        //CRM_Workflow_hook::testHook();

        // current set id
        $this->_wid      = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);

        // setting title for html page
        $title = ts('New Workflow');
        if ($this->_wid) {
            $title = CRM_Workflow_BAO_Workflow::getName($this->_wid);
        }
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $title = ts('Edit %1', array(1 => $title));
        }
        elseif ($this->_action & CRM_Core_Action::VIEW) {
            $title = ts('Preview %1', array(1 => $title));
        }
        CRM_Utils_System::setTitle($title);


        $this->set('BAOName', 'CRM_Workflow_BAO_Workflow');
        parent::preProcess();
    }

    function buildQuickForm() {

        /**
         *
         * id
         * name
         * description
         * require_login
         * is_active
         *
         */
        $this->assign('wid', $this->_wid);

        // add form elements
        $this->add(
            'text', // field type
            'name', // field name
            ts('Name'), // field label
            array("maxlength" => 255, "size" => 45),
            true // is required
        );
        $this->addRule('name', ts('You must supply a name for this Workflow'), 'required');
        $this->add(
            'textarea',
            'description',
            ts('Description'),
            array("rows" => 3, "cols" => 80)
        );

        $entities = array();
        $entities[] = array('entity_name' => 'contact_1', 'entity_type' => 'IndividualModel');
        $allowCoreTypes = array_merge(array('Contact', 'Individual'), CRM_Contact_BAO_ContactType::subTypes('Individual'));
        $allowSubTypes = array();

        $this->addProfileSelector('login_form_id', ts('Login Profile'), $allowCoreTypes, $allowSubTypes, $entities);

        /*
        $this->add(
            'select',
            'login_form_id',
            ts('Login Profile'),
            array('' => ts('- select profile -')) + $profiles,
            false
        );
        */
        $this->add(
            'checkbox',
            'require_login',
            ts('Require Login')
        );
        $this->addElement(
            'checkbox',
            'is_active',
            ts('Is this workflow active?')
        );

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


    function setDefaultValues() {
        $origID = null;
        $defaults = array();

        if ($this->_action & CRM_Core_Action::COPY) {
            $origID = $this->_cloneID;
        }
        else if ($this->_action & (CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE)) {
            $origID = $this->_wid;
        }

        if ($origID) {
            $params = array('id' => $origID);
            CRM_Workflow_BAO_Workflow::retrieve($params, $defaults);
        }
        $defaults['is_active'] = $origID ? CRM_Utils_Array::value('is_active', $defaults) : 1;
        $defaults['require_login'] = $origID ? CRM_Utils_Array::value('require_login', $defaults) : 1;

        return $defaults;
    }

    function postProcess() {
        $params = $this->controller->exportValues('Workflow');
        $params['is_active'] = CRM_Utils_Array::value('is_active', $params, FALSE);
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $params['id'] = $this->_wid;
        }

        $wf = CRM_Workflow_BAO_Workflow::add($params);

        CRM_Core_Session::setStatus(ts('The workflow \'%1\' has been saved.',
            array(1 => $wf->name)), "Saved", "success");


        //die(var_dump($tmp));
        parent::postProcess();
        return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/workflows'));
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
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }
}
