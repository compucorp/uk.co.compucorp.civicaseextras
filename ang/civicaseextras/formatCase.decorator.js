(function (angular, caseStatuses) {
  var module = angular.module('civicase');

  module.config(function ($provide) {
    $provide.decorator('formatCase', function ($delegate) {
      var originalFormatCase = $delegate;

      /**
       * Enhances the formatCase method so it adds information about overfue dates for the case.
       *
       * @param {Object} caseItem
       * @return {Object}
       */
      return function formatCase (caseItem) {
        var isStatusOpen = caseStatuses[caseItem.status_id].grouping === 'Opened';
        var isModifiedDateOverdue = moment().subtract(3, 'months').isSameOrAfter(caseItem.modified_date);
        caseItem = originalFormatCase(caseItem);
        caseItem.overdueDates = {
          modified_date: isStatusOpen && isModifiedDateOverdue
        };

        return caseItem;
      };
    });
  });
})(angular, CRM.civicase.caseStatuses);
