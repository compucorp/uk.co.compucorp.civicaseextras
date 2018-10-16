/* eslint-env jasmine */

(function (_) {
  describe('CiviCaseExtrasCaseOutcome controller', function () {
    var $controller, $q, $rootScope, $scope, activitiesMockData, ActivityTypesData, crmApi,
      CiviCaseExtrasCustomFields, customFieldsMockData, customFieldsMockFieldsMap;

    beforeEach(module('civicaseextras', 'civicase.data', function ($provide) {
      crmApi = jasmine.createSpy('crmApi');

      $provide.value('crmApi', crmApi);
    }));

    beforeEach(inject(function (_$controller_, _$q_, _$rootScope_, _activitiesMockData_,
      _ActivityTypesData_, _CiviCaseExtrasCustomFields_, _customFieldsMockData_) {
      $controller = _$controller_;
      $q = _$q_;
      $rootScope = _$rootScope_;
      activitiesMockData = _activitiesMockData_.get();
      ActivityTypesData = _.cloneDeep(_ActivityTypesData_);
      CiviCaseExtrasCustomFields = _CiviCaseExtrasCustomFields_;
      customFieldsMockData = _customFieldsMockData_.get();
      customFieldsMockFieldsMap = _customFieldsMockData_.getFieldsMap();
      CiviCaseExtrasCustomFields.get = jasmine.createSpy('get');

      CiviCaseExtrasCustomFields.get.and.returnValue(customFieldsMockFieldsMap);
      crmApi.and.returnValue($q.resolve({
        count: activitiesMockData.length,
        values: activitiesMockData
      }));
    }));

    describe('on init', function () {
      var expectedOutcomes = [];

      beforeEach(function () {
        activitiesMockData.forEach(function (activity) {
          customFieldsMockData.forEach(function (customField) {
            activity['custom_' + customField.id] = _.random(0, 9999);
          });
        });

        expectedOutcomes = _.map(activitiesMockData, function (activity) {
          var activityType = ActivityTypesData.values[activity.activity_type_id].label;
          var customFields = _.chain(activity)
            .pick(function (value, fieldName) {
              return _.startsWith(fieldName, 'custom_');
            })
            .map(function (value, fieldName) {
              return {
                label: customFieldsMockFieldsMap[fieldName].label,
                value: value
              };
            })
            .value();

          return {
            activityType: activityType,
            customFields: customFields
          };
        });

        initController();
      });

      it('requests a list of activity outcomes', function () {
        expect(crmApi).toHaveBeenCalledWith('Activity', 'get', {
          'sequential': 1,
          'case_id': $scope.case.id,
          'activity_type_id.grouping': 'outcome'
        });
      });

      it('stores a list of activity outcomes and their custom fields', function () {
        expect($scope.activityOutcomes).toEqual(expectedOutcomes);
      });
    });

    describe('when the case details have been updated', function () {
      beforeEach(function () {
        initController();
        $rootScope.$emit('updateCaseData');
      });

      it('refreshes the case outcome data', function () {
        expect(crmApi).toHaveBeenCalledWith('Activity', 'get', {
          'sequential': 1,
          'case_id': $scope.case.id,
          'activity_type_id.grouping': 'outcome'
        });
      });
    });

    function initController () {
      $scope = $rootScope.$new();
      $scope.case = { id: _.uniqueId() };

      $controller('civicaseExtrasCaseOutcomeController', { $scope: $scope });
      $rootScope.$digest();
    }
  });
})(CRM._);
