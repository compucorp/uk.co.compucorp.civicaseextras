<?php

use CRM_Civicaseextras_Services_CustomValueService as CustomValueService;

class CRM_Civicaseextras_APIWrappers_Case_Getcaselist implements API_Wrapper {

  /**
   * @var array
   *   An instance of the custom value service.
   */
  protected $customValueService;

  /**
   * @param CustomValueService $customValueService
   */
  public function __construct(CustomValueService $customValueService) {
    $this->customValueService = $customValueService;
  }

  /**
   * {@inheritDoc} It Alters the Case Getcaselist action so it includes the case duration
   * custom field to the response.
   */
  public function fromApiInput($apiRequest) {
    if (!$this->shouldRun($apiRequest)) {
      return $apiRequest;
    }

    $return = CRM_Utils_Array::value('return', $apiRequest['params'], []);
    $caseDuration = $this->customValueService->getCustomField('case_stats', 'duration');

    if ($caseDuration) {
      $return[] = 'custom_' . $caseDuration['id'];
    }

    $apiRequest['params']['return'] = $return;

    return $apiRequest;
  }

  /**
   * {@inheritDoc}
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

  /**
   * Determines if the API Wrapper should run by checking that the request is for
   * the Case entity and the action is "getcaselist".
   *
   * @param array $apiRequest
   * @return bool
   */
  public function shouldRun($apiRequest) {
    return $apiRequest['entity'] === 'Case' && $apiRequest['action'] === 'getcaselist';
  }

}
