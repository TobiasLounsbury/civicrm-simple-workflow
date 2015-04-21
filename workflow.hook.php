<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 6/9/14
 * Time: 3:14 PM
 */

class CRM_Workflow_hook {

    static $_nullObject = NULL;

    /**
     * Generate a default CRUD URL for an entity
     *
     * @param int $wid: The ID of the Workflow for which details are being saved
     * @param array $data:
     * @return mixed
     */
    static function beforeSave($wid, &$data) {
        return CRM_Utils_Hook::singleton()->invoke(2, $wid, $data,
            self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
            'workflow_beforeSave'
        );
    }

    static function afterSave($wid, &$data) {
        return CRM_Utils_Hook::singleton()->invoke(2, $wid, $data,
            self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
            'workflow_afterSave'
        );
    }

}