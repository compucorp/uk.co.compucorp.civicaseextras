<?php

class CRM_Civicaseextras_APIWrappers_Case_Getcaselist implements API_Wrapper {
    /**
     * Implements API_Wrapper::fromApiInput().
     *
     * Alters the Case Getcaselist action so it includes the case duration custom field to the response.
     *
     * @param array $apiRequest
     * @return array
     */
    public function fromApiInput($apiRequest) {
      $return = CRM_Utils_Array::value('return', $apiRequest['params'], []);
      $caseDuration = _civicaseextras_get_caseDurationField();

      if ($caseDuration) {
        $return[] = 'custom_' . $caseDuration['id'];
      }

      $apiRequest['params']['return'] = $return;

      return $apiRequest;
    }

    /**
     * Implements API_Wrapper::toApiOutput().
     *
     * @param array $apiRequest
     * @param array $result
     * @return array
     */
    public function toApiOutput($apiRequest, $result) {
      return $result;
    }
  }
