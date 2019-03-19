(function (angular, caseStatuses, civicaseextras) {
  var module = angular.module('civicase');

  module.config(function ($provide) {
    $provide.decorator('formatCase', formatCaseDecorator);
  });

  function formatCaseDecorator ($delegate) {
    var originalFormatCase = $delegate;

    /**
     * Enhances the formatCase method so it adds information about overdue dates for the case.
     *
     * @param {Object} caseItem
     * @return {Object}
     */
    return function formatCase (caseItem) {
      var overdueNotificationLimit = parseInt(civicaseextras.overdueNotificationLimit, 10);
      var isStatusOpen = caseStatuses[caseItem.status_id].grouping === 'Opened';
      var isModifiedDateOverdue = moment().subtract(overdueNotificationLimit, 'days')
        .isSameOrAfter(caseItem.modified_date);
      caseItem = originalFormatCase(caseItem);
      caseItem.overdueDates = {
        modified_date: isStatusOpen && isModifiedDateOverdue
      };

      return caseItem;
    };
  }
})(
  angular,
  CRM.civicase.caseStatuses,
  CRM.civicaseextras
);
