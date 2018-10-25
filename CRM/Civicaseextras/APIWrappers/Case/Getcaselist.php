<?php

class CRM_Civicaseextras_APIWrappers_Case_Getcaselist implements API_Wrapper {

  /**
   * {@inheritDoc} It Alters the Case Getcaselist action so it includes the case duration
   * custom field to the response.
   */
  public function fromApiInput($apiRequest) {
    if (!$this->shouldRun($apiRequest)) {
      return $apiRequest;
    }

    $return = CRM_Utils_Array::value('return', $apiRequest['params'], []);
    $caseDuration = CRM_Civicaseextras_Services_CustomValueService::getCustomField('case_stats', 'duration');

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
