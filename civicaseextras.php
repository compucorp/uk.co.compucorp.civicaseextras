<?php

require_once 'civicaseextras.civix.php';
use CRM_Civicaseextras_ExtensionUtil as E;
use \Civi\Angular\ChangeSet as AngularChangeSet;
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
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterAngular/
 */
function civicaseextras_civicrm_alterAngular(AngularManager $angular) {
  _civicaseextras_alterAngular_addVisualAlert($angular);
  _civicaseextras_alterAngular_appendOutcomePanel($angular);
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterContent/
 */
function civicaseextras_civicrm_alterContent (&$content, $context, $templateName, $form) {
  $isViewingTheCaseAdminForm = get_class($form) === CRM_Admin_Form_Setting_Case::class;

  if ($isViewingTheCaseAdminForm) {
    _civicaseextras_alterContent_addCivicaseAdminSettingsFields($content);
  }
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess/
 */
function civicaseextras_civicrm_preProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Setting_Case') {
    _civicaseextras_preProcess_addCivicaseAdminSettingsFieldsReference($form);
  }
}

/**
 * Adds extra settings fields to the Civicase Admin Settings form.
 *
 * @param string $content the original form content.
 */
function _civicaseextras_alterContent_addCivicaseAdminSettingsFields (&$content) {
  $settingsTemplateHtml = _civicaseextras_getTemplateContent('CRM/Civicaseextra/Admin/Form/Settings.tpl');

  $doc = phpQuery::newDocumentHTML($content);
  $doc->find('table.form-layout tr:last')->append($settingsTemplateHtml);

  $content = $doc->getDocument();
}

/**
 * It adds extra settings to the Civicase settings page.
 *
 * @param object $form a reference to the civicase admin form.
 */
function _civicaseextras_preProcess_addCivicaseAdminSettingsFieldsReference (&$form) {
  $settings = $form->getVar('_settings');
  $settings['civicaseCaseLastUpdatedNotificationLimit'] = CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME;

  $form->setVar('_settings', $settings);
}

/**
 * Replaces the date column type on case lists so it displays an alert when the
 * given date is overdue.
 *
 * @param $angular AngularManager
 */
function _civicaseextras_alterAngular_addVisualAlert (AngularManager &$angular) {
  $changeSet = AngularChangeSet::create('display_warning_for_overdue_cases')
    ->alterHtml('~/civicase/case/list/directives/case-list-table.directive.html',
      function (phpQueryObject $doc) {
        $caseListTableHtml = _civicaseextras_getTemplateContent('CRM/Civicaseextra/Partials/CaseListTable.tpl');

        $doc->find('[ng-switch-when="date"]')
          ->html($caseListTableHtml);
      });

  $angular->add($changeSet);
}

/**
 * Appends the case outcomes to the case details summary.
 *
 * @param $angular AngularManager
 */
function _civicaseextras_alterAngular_appendOutcomePanel (AngularManager &$angular) {
  $changeSet = AngularChangeSet::create('inject_case_outcomes')
    ->alterHtml('~/civicase/case/details/summary-tab/case-summary-custom-data.html',
      function (phpQueryObject $doc) {
        $doc->find('civicase-masonry-grid')
          ->prepend('<civicase-extras-case-outcome case="item"></civicase-extras-case-outcome>');
      });
  $angular->add($changeSet);
}

/**
 * Returns the HTML generated by the smarty template located at the given path.
 *
 * @param string $templatePath the path tot he smarty template.
 * @param array $vars the list of vars and their values to pass to the template.
 *
 * @return string
 */
function _civicaseextras_getTemplateContent ($templatePath, $vars = []) {
  $smarty = &CRM_Core_Smarty::singleton();

  return $smarty->fetchWith($templatePath, $vars);
}
