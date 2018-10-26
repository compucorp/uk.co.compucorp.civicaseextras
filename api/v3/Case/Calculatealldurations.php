<?php

use CRM_CiviCaseextras_Services_ActivityTypesService as ActivityTypesService;

/**
 * API call to calculate the duration of all cases.
 */
function civicrm_api3_case_calculatealldurations() {
  $activityTypesService = new ActivityTypesService();

  $logger = new CRM_CiviCaseextras_CaseDurationLog($activityTypesService);
  $logger->calculateAllCasesDuration();

  return array();
}
