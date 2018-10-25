<?php

require_once 'civicaseextras.civix.php';
use CRM_Civicaseextras_ExtensionUtil as E;
use \Civi\Angular\Manager as AngularManager;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicaseextras_civicrm_config(&$config) {
  _civicaseextras_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicaseextras_civicrm_xmlMenu(&$files) {
  _civicaseextras_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicaseextras_civicrm_install() {
  _civicaseextras_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civicaseextras_civicrm_postInstall() {
  _civicaseextras_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicaseextras_civicrm_uninstall() {
  _civicaseextras_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicaseextras_civicrm_enable() {
  _civicaseextras_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicaseextras_civicrm_disable() {
  _civicaseextras_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicaseextras_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicaseextras_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civicaseextras_civicrm_managed(&$entities) {
  _civicaseextras_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicaseextras_civicrm_caseTypes(&$caseTypes) {
  _civicaseextras_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civicaseextras_civicrm_angularModules(&$angularModules) {
  _civicaseextras_civix_civicrm_angularModules($angularModules);
  _civicaseextras_addCiviCaseExtrasAsRequirementForCivicase($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicaseextras_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicaseextras_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function civicaseextras_civicrm_entityTypes(&$entityTypes) {
  _civicaseextras_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_alterAngular().
 */
function civicaseextras_civicrm_alterAngular(AngularManager $angular) {
  $modifiers = [
    new CRM_Civicaseextras_AngularModifiers_CaseDuration($angular),
    new CRM_Civicaseextras_AngularModifiers_OutcomesPanel($angular),
  ];

  foreach ($modifiers as $modifier) {
    $modifier->runModifications();
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function civicaseextras_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  $wrappers[] = new CRM_Civicaseextras_APIWrappers_Case_Getcaselist();
}

/**
 * Add civicase extras as a requirement of civicase
 *
 * @param Array $angularModules
 */
function _civicaseextras_addCiviCaseExtrasAsRequirementForCivicase(&$angularModules) {
  if (isset($angularModules['civicase'])) {
    $angularModules['civicase']['requires'][] = 'civicaseextras';
  } else {
    CRM_Core_Session::setStatus(
      'The <strong>Civicase Extras</strong> extension requires <strong>CiviCase</strong> to be installed first.',
      'Warning',
      'no-popup'
    );
  }
}

/**
 * Returns the details for the case duration custom fields.
 *
 * @return array
 */
function _civicaseextras_get_caseDurationField () {
  $caseDuration = civicrm_api3('CustomField', 'get', [
    'custom_group_id' => 'Case_Stats',
    'name' => 'duration'
  ]);

  return CRM_Utils_Array::first($caseDuration['values'], []);
}


// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function civicaseextras_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function civicaseextras_civicrm_navigationMenu(&$menu) {
  _civicaseextras_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _civicaseextras_civix_navigationMenu($menu);
} // */
