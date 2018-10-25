<?php

use Civi\Angular\Manager as AngularManager;
use Civi\Angular\ChangeSet as AngularChangeSet;
use CRM_Civicaseextras_Services_CustomValueService as CustomValueService;

class CRM_Civicaseextras_AngularModifiers_CaseDuration {

  /**
   * @var AngularManager
   *   A reference to the Angular Manager object as provided by the alter angular hook.
   */
  protected $angular;

  /**
   * @var array
   *   The case duration custom field information.
   */
  protected $caseDuration;

  /**
   * @param AngularManager $angular as provided by the alter angular hook.
   * @param CustomValueService $customValueService an instance of the custom value service.
   */
  public function __construct(AngularManager &$angular, CustomValueService $customValueService) {
    $this->angular = $angular;
    $this->caseDuration = $customValueService->getCustomField('case_stats', 'duration');
  }

  /**
   * Adds the case duration column and value to the case list table template of civicase.
   */
  public function runModifications() {
    $changeSet = AngularChangeSet::create('inject_case_duration')
      ->alterHtml('~/civicase/CaseListTable.html',
        function (phpQueryObject $doc) {
          $this->addCaseDurationTableHeader($doc);
          $this->addCaseDurationTableCell($doc);
        });

    $this->angular->add($changeSet);
  }

  /**
   * Adds the case duration table header to the civicase list table.
   *
   * @param phpQueryObject $doc
   */
  protected function addCaseDurationTableHeader(phpQueryObject &$doc) {
    $doc->find('.civicase__case-list-table thead tr')
      ->append('<th
        ng-show="!viewingCase && headers.length"
        civicase-case-list-sort-header="custom_' . $this->caseDuration['id'] . '">
        ' . $this->caseDuration['label'] . '
      </th>');
  }

  /**
   * Adds the case duration table cell to the civicase list table.
   *
   * @param phpQueryObject $doc
   */
  protected function addCaseDurationTableCell(phpQueryObject &$doc) {
    $doc->find('.civicase__case-list-table tbody tr[ng-repeat="item in cases"]')
      ->append('<td ng-show="!viewingCase && headers.length">
        {{item.custom_' . $this->caseDuration['id'] . '}}
      </td>');
  }

}
