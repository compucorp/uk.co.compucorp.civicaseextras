(function (_, angular, activityTypes) {
  var module = angular.module('civicaseextras');

  module.directive('civicaseExtrasCaseOutcome', function () {
    return {
      require: '^civicaseMasonryGrid',
      scope: { case: '=' },
      replace: true,
      templateUrl: '~/civicaseextras/CaseOutcome.html',
      controller: 'civicaseExtrasCaseOutcomeController',
      link: civicaseExtrasCaseOutcomeLink
    };

    function civicaseExtrasCaseOutcomeLink ($scope, $element, attrs, masonryGrid) {
      (function init () {
        $scope.$on('civicaseExtrasCaseOutcome::loaded', oncaseOutcomeLoaded);
      })();

      function oncaseOutcomeLoaded () {
        if ($scope.activityOutcomes.length) {
          masonryGrid.addGridItemAt($element, 0);
          $element.show();
        } else {
          masonryGrid.removeGridItem($element);
          $element.hide();
        }
      }
    }
  });

  module.controller('civicaseExtrasCaseOutcomeController', function ($q, $scope, crmApi, CiviCaseExtrasCustomFields) {
    var customFieldsMap = {};
    $scope.activityOutcomes = [];

    (function init () {
      loadCaseOutcomesData();

      $scope.$on('updateCaseData', loadCaseOutcomesData);
    })();

    /**
     * Requests a list of activities of the "outcome" group.
     *
     * @return {Promise}
     */
    function getActivityOutcomes () {
      return crmApi('Activity', 'get', {
        'sequential': 1,
        'case_id': $scope.case.id,
        'activity_type_id.grouping': 'outcome'
      });
    }

    /**
     * Loads the custom fields and activity outcomes data. The data is stored
     * and prepared for use by the view.
     */
    function loadCaseOutcomesData () {
      $q.all([
        CiviCaseExtrasCustomFields.get(),
        getActivityOutcomes()
      ])
        .then(function (results) {
          customFieldsMap = results[0];

          storeActivityOutcomes(results[1].values);
        })
        .finally(function () {
          $scope.$emit('civicaseExtrasCaseOutcome::loaded');
        });
    }

    /**
     * Stores the activity outcomes as a list of activity type and custom fields
     * that belong to the outcome.
     *
     * @param {Array} activityOutcome a list of activities that belong to the "outcome" group.
     */
    function storeActivityOutcomes (activityOutcomes) {
      $scope.activityOutcomes = _.map(activityOutcomes, function (activityOutcome) {
        var activityType = activityTypes[activityOutcome.activity_type_id].label;
        var customFields = getCustomFieldsForActivity(activityOutcome);

        return {
          activityType: activityType,
          customFields: customFields
        };
      });
    }

    /**
     * Returns a list of custom fields for the given activity. Any field that
     * starts with `custom_` will be returned as a pair of the custom field label
     * and the field value (Ex: { label: 'Custom Outcome Field', value: 123 }).
     *
     * @param {Object} activity the activity containing the custom fields.
     * @return {Array} the list of custom fields belonging to the activity.
     */
    function getCustomFieldsForActivity (activity) {
      return _.chain(activity)
        .pick(function (value, fieldName) {
          return _.startsWith(fieldName, 'custom_');
        })
        .map(function (value, fieldName) {
          return {
            label: customFieldsMap[fieldName].label,
            value: value
          };
        })
        .value();
    }
  });
})(CRM._, angular, CRM.civicase.activityTypes);
