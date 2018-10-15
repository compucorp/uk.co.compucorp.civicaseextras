(function (_, angular) {
  var module = angular.module('civicaseextras');

  module.directive('civicaseExtrasOutcome', function () {
    return {
      scope: {
        case: '='
      },
      templateUrl: '~/civicaseextras/Outcomes.html',
      controller: 'civicaseExtrasOutcomeController'
    };
  });

  module.controller('civicaseExtrasOutcomeController', function ($scope, crmApi) {
    $scope.activities = [];

    (function init () {
      crmApi([
        ['Activity', 'get', {
          'sequential': 1,
          'case_id': $scope.case.id,
          'activity_type_id.grouping': 'outcome'
        }],
        ['CustomField', 'get', {
          'options': { 'limit': 0 },
          'return': [ 'label' ]
        }]
      ])
        .then(function (results) {
          var customFields = results[1].values;
          $scope.activities = _.cloneDeep(results[0].values);

          customFields = _.indexBy(customFields, function (customField) {
            return 'custom_' + customField.id;
          });

          _.forEach($scope.activities, function (activity) {
            activity['activityType.label'] = CRM.civicase.activityTypes[activity.activity_type_id].label;
            activity.customFields = _.chain(activity)
              .pick(function (value, fieldName) {
                return _.startsWith(fieldName, 'custom_');
              })
              .map(function (value, fieldName) {
                return {
                  label: customFields[fieldName].label,
                  value: value
                };
              })
              .value();
          });

          console.log('#', $scope.activities);
        });
    })();
  });
})(CRM._, angular);
