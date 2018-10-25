<?php

use Civi\Angular\Manager as AngularManager;
use Civi\Angular\ChangeSet as AngularChangeSet;

class CRM_Civicaseextras_AngularModifiers_OutcomesPanel {

  /**
   * @var AngularManager
   *   A reference to the Angular Manager object as provided by the alter angular hook.
   */
  protected $angular;

  /**
   * @param AngularManager $angular as provided by the alter angular hook.
   */
  public function __construct(AngularManager &$angular) {
    $this->angular = $angular;
  }

  /**
   * Adds the case outcomes panel to the case details template of civicase.
   */
  public function runModifications() {
    $changeSet = AngularChangeSet::create('inject_case_outcomes')
      ->alterHtml('~/civicase/CaseDetails--tabs--summary--CustomData.html',
        function (phpQueryObject $doc) {
          $doc->find('civicase-masonry-grid')
            ->prepend('<civicase-extras-case-outcome case="item"></civicase-extras-case-outcome>');
        });

    $this->angular->add($changeSet);
  }

}
