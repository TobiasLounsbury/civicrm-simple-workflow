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
   * This hook is called before saving details for a workflow
   *
   * @param $wid
   * @param $data
   * @return mixed
   */
  static function beforeSave($wid, &$data) {
    return CRM_Utils_Hook::singleton()->invoke(2, $wid, $data,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'workflow_beforeSave'
    );
  }

  /**
   * This hook is called after save of a workflow
   *
   * @param $wid
   * @param $data
   * @return mixed
   */
  static function afterSave($wid, &$data) {
    return CRM_Utils_Hook::singleton()->invoke(2, $wid, $data,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'workflow_afterSave'
    );
  }

  /**
   * This function allows 3rd party extensions to modify the load params
   * of an individual step before it is loaded.
   *
   * @param $step
   * @param $urlParams
   * @param $settings
   * @return mixed
   */
  static function getStepParams($step, &$urlParams, &$settings) {
    return CRM_Utils_Hook::singleton()->invoke(3, $step, $urlParams, $settings,
      self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'workflow_getStepParams'
    );
  }

  static function getStepTypes(&$uiTemplates, &$typeTemplates, &$javaScript, &$css) {
    return CRM_Utils_Hook::singleton()->invoke(4, $uiTemplates, $typeTemplates, $javaScript, $css,
      self::$_nullObject, self::$_nullObject,
      'workflow_getStepTypes'
    );
  }


  static function execute($context, &$form) {
    return CRM_Utils_Hook::singleton()->invoke(2, $context, $form,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'workflow_execute'
    );
  }
}