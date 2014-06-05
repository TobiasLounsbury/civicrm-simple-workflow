<?php

//TODO: Verify Permissions
//TODO: Work on Force Login
//TODO: Uncomment Contribution page check and forward

require_once 'CRM/Core/Page.php';

class CRM_Workflow_Page_Execute extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle("");
        $wid = CRM_Utils_Request::retrieve('wid', 'Positive', $this, false, 0);
        $workflow = null;
        $steps = array();

        if ($wid) {
            $wsql = "SELECT * FROM civicrm_workflow WHERE id = {$wid} LIMIT 1";
            $dao =& CRM_Core_DAO::executeQuery($wsql);
            if ($dao->fetch()) {
                $workflow = (array) $dao;
            }

            $dsql = "SELECT * FROM civicrm_workflow_detail WHERE workflow_id = {$wid} ORDER BY `order`";
            $dao =& CRM_Core_DAO::executeQuery($dsql);


            while ($dao->fetch()) {
                $steps[$dao->order] = (array) $dao;
                //If this workflow contains a contribution page forward to it
                //And the page injector will take over
                if ($dao->entity_table == "Page") {
                    return CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/contribute/transact', 'reset=1&id=' . $dao->entity_id));
                }
            }
        }

        if ($workflow['is_active']) {
            //Let the page know the method we are using
            CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('method' => "template")));

            //Add Stylesheet
            CRM_Core_Resources::singleton()->addStyleFile('org.botany.workflow', 'workflow_execute.css');

            //Add Javascript files and settings
            CRM_Core_Resources::singleton()->addScriptFile('org.botany.workflow', 'workflow_execute.js');
            CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('steps' => $steps)));

            //Set the width for the step lis
            $stepWidth = (empty($steps)) ? 98 : round(98 / sizeof($steps), 0, PHP_ROUND_HALF_DOWN);
            CRM_Core_Resources::singleton()->addSetting(array('Workflow' => array('breadcrumWidth' => $stepWidth)));

            //Assign the data so smarty can use it
            $this->assign('workflow', $workflow);
            $this->assign('steps', $steps);
        } else {
            //Do something to say you can't use a disabled workflow

        }

        parent::run();
    }
}
