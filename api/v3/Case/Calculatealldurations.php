<?php

use CRM_CiviCaseextras_CaseDurationLog as CaseDurationLog;

/**
 * API call to calculate the duration of all cases.
 */
function civicrm_api3_case_calculatealldurations() {
  $logger = CaseDurationLog::fabricate();
  $logger->calculateAllCasesDuration();

  return array();
}
