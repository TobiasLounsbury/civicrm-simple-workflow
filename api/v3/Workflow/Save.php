<?php

/**
 * Workflow.save API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_workflow_save_spec(&$spec) {
    $spec['data']['api.required'] = 1;
    $spec['wid']['api.required'] = 1;
}

/**
 * Workflow.save API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_workflow_save($params) {
    if (!array_key_exists("data", $params) || !$params['data']) {
        throw new API_Exception('Missing parameter: data',  170);
    }
    if (!array_key_exists("wid", $params) || !$params['wid']) {
        throw new API_Exception('Missing parameter: wid',  170);
    }

    $wid = $params['wid'];
    $d = urldecode($params['data']);
    $data = parse_str($d);
    //I don't know why this is important, but it made things work.
    $try2 = parse_str($params['data']);

    //Hook in case someone wants to alter the data
    CRM_Workflow_hook::beforeSave($wid, $data);

    $transaction = new CRM_Core_Transaction();

    $dsql = "DELETE FROM civicrm_workflow_detail WHERE workflow_id = {$wid}";
    $dao =& CRM_Core_DAO::executeQuery($dsql);

    if (!empty($data)) {
        $sql = "INSERT INTO civicrm_workflow_detail ( workflow_id, entity_table, entity_id, `order`, breadcrumb, `next`, title) VALUES ";
        $i = 1;
        $vals = array();
        foreach($data as $key => $d) {
            //$did = (strpos($id, ":")) ? 0 : $id;
            $eid = $d['entity_id'];
            $e_type = $d['entity_table'];
            $order = $d['order'];
            $breadcrumb = $d['breadcrumb'];
            $next = $d['next'];
            $title = $d['title'];
            $sql = $sql. "( %". ($i+0) .", %". ($i+1) .", %". ($i+2) .", %". ($i+3) .", %". ($i+4) .", %". ($i+5) .", %". ($i+6) ."),";
            

            //$vals[$i++] = array($did, 'Integer');
            $vals[$i++] = array($wid, 'Integer');
            $vals[$i++] = array($e_type, 'String');
            $vals[$i++] = array($eid, 'String');
            $vals[$i++] = array($order, 'Integer');
            $vals[$i++] = array($breadcrumb, 'String');
            $vals[$i++] = array($next, 'String');
            $vals[$i++] = array($title, 'String');
        }
        $sql = substr($sql, 0, -1);
        try {
            $dao =& CRM_Core_DAO::executeQuery($sql, $vals);
        } catch (Exception $e) {
            $transaction->rollback();
            return civicrm_api3_create_error($e.message);
        }
        //TODO: Better error checking?
        if ($dao->_lastError) {
            $transaction->rollback();

        } else {
            $transaction->commit();
            CRM_Workflow_hook::afterSave($wid, $data);
        }

    }
    $returnValues = array();
    return civicrm_api3_create_success($returnValues, $params, 'Workflow', 'Save');
}

