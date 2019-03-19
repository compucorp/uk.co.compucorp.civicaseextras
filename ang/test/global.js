/* eslint-env jasmine */

(function (CRM) {
  CRM.civicase = {};
  CRM.civicaseextras = {};
  CRM.angular = { requires: {} };
  /**
   * Dependency Injection for civicase module, defined in ang/civicase.ang.php
   * For unit testing they needs to be mentioned here
   */
  CRM.angular.requires['civicaseextras'] = [];
}(CRM));
