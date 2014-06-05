<?php

//TODO: Change tablename to civicrm_workflow

require_once 'CRM/Workflow/DAO/Workflow.php';

class CRM_Workflow_BAO_Workflow extends CRM_Workflow_DAO_Workflow {

    /**
     * class constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Takes an associative array and creates a new workflow
     *
     * This function extracts all the params it needs to initialize the created
     * Workflow. The params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Workflow_DAO_Workflow object
     * @access public
     * @static
     */
    static function &add(&$params) {
        //require_once 'CRM/Utils/Date.php';

        $workf = new CRM_Workflow_DAO_Workflow();
        $workf->name = $params['name'];
        $workf->description = $params['description'];
        $workf->login_form_id = $params['login_form_id'];

        if (! empty($params['id'])) {
            $workf->id = $params['id'];
        }

        $workf->is_active = CRM_Utils_Array::value('is_active', $params) ? 1 : 0;
        $workf->require_login = CRM_Utils_Array::value('require_login', $params) ? 1 : 0;

        $id = empty($params['id']) ? NULL : $params['id'];
        $op = $id ? 'edit' : 'create';
        CRM_Utils_Hook::pre($op, 'Workflow', $id, $params);
        $workf->save();
        CRM_Utils_Hook::post($op, 'Workflow', $workf->id, $workf);

        return $workf;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference) an assoc array of name/value pairs
     * @param array $defaults (reference) an assoc array to hold the flattened values
     *
     * @return object CDM_BAO_Item object on success, null otherwise
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults) {
        $item = new CRM_Workflow_DAO_Workflow();
        $item->copyValues($params);
        if ($item->find(true)) {
            CRM_Core_DAO::storeValues($item, $defaults);
            return $item;
        }
        return null;
    }

    static function getWorkflow($wid) {
        if (!$wid) {
            return null;
        }
        $dsql = "SELECT * FROM civicrm_workflow WHERE id = {$wid} LIMIT 1";
        $dao =& CRM_Core_DAO::executeQuery($dsql);

        $result = null;
        if ($dao->fetch()) {
            $result = (array) $dao;
        }
        return $result;
    }

    static function getWorkflowDetails($wid, $AllowNullBreadcrumbs = true) {
        $extraWhere = ($AllowNullBreadcrumbs) ? "" : " AND breadcrumb <> ''";
        $dsql = "SELECT * FROM civicrm_workflow_detail WHERE workflow_id = {$wid}{$extraWhere} ORDER BY `order`";
        $dao =& CRM_Core_DAO::executeQuery($dsql);
        $steps = array();
        while ($dao->fetch()) {
            $steps[$dao->order] = (array) $dao;
            $steps[$dao->order]['options'] = json_decode($dao->options);
        }
        return $steps;
    }

    static function getWorkflowPages($is_active = true) {
        if ($is_active) {
            $sql = "SELECT workflow_id,entity_id FROM civicrm_workflow_detail LEFT JOIN civicrm_workflow on workflow_id=civicrm_workflow.id WHERE entity_table = 'Page' AND is_active = 1";
        } else {
            $sql = "SELECT workflow_id,entity_id FROM civicrm_workflow_detail WHERE entity_table = 'Page'";
        }
        $result = array();
        $dao =& CRM_Core_DAO::executeQuery($sql, array());
        while ($dao->fetch()) {
            $result[$dao->entity_id] = $dao->workflow_id;
        }
        return $result;
    }

    static function getWorkflows() {
        $discounts = array();

        $sql = "
SELECT  id,
    name,
    description,
    login_form_id,
    require_login,
    is_active
FROM    civicrm_workflow
";
        $dao =& CRM_Core_DAO::executeQuery($sql, array());
        $allLinks = CRM_Workflow_Page_Workflow_List::actionLinks();
        while ($dao->fetch()) {

            $result[$dao->id] = (array) $dao;

            // form all action links
            $action = array_sum(array_keys($allLinks));

            // update enable/disable links depending on price_set properties.

            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            }
            else {
                $action -= CRM_Core_Action::DISABLE;
            }

            $actionLinks = $allLinks;
            //CRM-10117
            $result[$dao->id]['action'] = CRM_Core_Action::formLink($actionLinks, $action,
                array('wid' => $dao->id),
                ts('more'),
                FALSE,
                'workflow.row.actions',
                'Workflow',
                $dao->id
            );
        }
        return (empty($result)) ? false : $result;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     *
     * @access public
     * @static
     */
    static function setIsActive($id, $is_active) {
        return CRM_Core_DAO::setFieldValue('CRM_Workflow_DAO_Workflow', $id, 'is_active', $is_active);
    }

    static function getName($wid) {
        if (!$wid) {
            return false;
        }
        $sql = "SELECT name FROM civicrm_workflow WHERE id = {$wid} LIMIT 1";
        $dao =& CRM_Core_DAO::executeQuery($sql);
        if (!$dao->fetch()) {
            return false;
        } else {
            $a = (array) $dao;
            return $a['name'];
        }
    }

    static function isEnabled($code) {
        if ($code['is_active'] == 1) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Function to delete workflow
     *
     * @param  wid int, Id of the workflow to be deleted.
     *
     * @access public
     * @static
     * @return true on success else false
     */
    static function del($wid) {
        $workflow = new CRM_Workflow_DAO_Workflow();
        $workflow->id = $wid;

        if ($workflow->find(TRUE)) {
            CRM_Utils_Hook::pre('delete', 'Workflow', $workflow->id, $workflow);

            $workflow->delete();

            $dsql = "DELETE FROM civicrm_workflow_detail WHERE workflow_id = {$wid}";
            $dao =& CRM_Core_DAO::executeQuery($dsql);

            CRM_Utils_Hook::post('delete', 'Workflow', $workflow->id, $workflow);

            return TRUE;
        }

        return FALSE;
    }

    static function copy($wid) {

        $sql = "SELECT * FROM civicrm_workflow WHERE id = {$wid}";
        $dao =& CRM_Core_DAO::executeQuery($sql);
        if ($dao->fetch()) {
            $workflow = (array) $dao;
            $workflow['id'] = null;
            $new_workflow = CRM_Workflow_BAO_Workflow::add($workflow);

            $sql2 = "SELECT * FROM civicrm_workflow_detail WHERE workflow_id = {$wid}";
            $dao2 =& CRM_Core_DAO::executeQuery($sql2);

            $nsql = "INSERT INTO civicrm_workflow_detail (id, workflow_id, entity_table, entity_id, `order`, breadcrumb) VALUES ";
            $nid = $new_workflow->id;
            while ($dao2->fetch()) {
                $did = 0;
                $eid = $dao2->entity_id;
                $e_type = $dao2->entity_table;
                $order = $dao2->order;
                $breadcrumb = $dao2->breadcrumb;
                $nsql .= "($did, $nid, '$e_type', $eid, $order, '$breadcrumb'),";
            }
            $nsql = substr($nsql, 0, -1);
            $dao =& CRM_Core_DAO::executeQuery($nsql);
        }
    }
}
