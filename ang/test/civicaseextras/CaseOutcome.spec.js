/* eslint-env jasmine */

(function (_, $) {
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
        activitiesMockData = getActivitiesWithMockedCustomFields();
        expectedOutcomes = getOutcomesActivities(activitiesMockData);

        crmApi.and.returnValue($q.resolve({
          count: activitiesMockData.length,
          values: activitiesMockData
        }));
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

      /**
       * Given an activity, it will return a list of all its custom fields and their
       * values. Ex.:
       * [
       *   { label: 'Real Custom Field Label', value: 'Activity Value' }
       * ]
       *
       * @param {Object} activity
       * @return {Array}
       */
      function getActivityCustomFields (activity) {
        return _.chain(activity)
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
      }

      /**
       * Returns a list of activities that have mocked custom fields and values.
       *
       * @return {Array}
       */
      function getActivitiesWithMockedCustomFields () {
        return _.cloneDeep(activitiesMockData)
          .map(function (activity) {
            customFieldsMockData.forEach(function (customField) {
              activity['custom_' + customField.id] = _.random(0, 9999);
            });

            return activity;
          });
      }

      /**
       * Returns a list of outcome activities and their custom fields.
       *
       * @param {Array} activities the activities to use as reference when returning their
       * activity type label and custom fields.
       * @return {Array} the result object contains the activity type label and its
       * custom fields and values. Ex.:
       * [
       *   activityType: 'Tribunal Outcome',
       *   customFields: [
       *     { label: 'Tribunal Outcome', value: 'Won' },
       *     { label: 'Settlement Team Outcome', value: 'Settled' },
       *     { label: 'ET Outcome', value: 'Lost' }
       *   ]
       * ]
       */
      function getOutcomesActivities (activities) {
        return _.map(activities, function (activity) {
          var activityType = ActivityTypesData.values[activity.activity_type_id].label;
          var customFields = getActivityCustomFields(activity);

          return {
            activityType: activityType,
            customFields: customFields
          };
        });
      }
    });

    describe('when the case details have been updated', function () {
      beforeEach(function () {
        initController();
        crmApi.calls.reset();
        $rootScope.$broadcast('updateCaseData');
        $rootScope.$digest();
      });

      it('refreshes the case outcome data', function () {
        expect(crmApi).toHaveBeenCalledWith('Activity', 'get', {
          'sequential': 1,
          'case_id': $scope.case.id,
          'activity_type_id.grouping': 'outcome'
        });
      });
    });

    /**
     * Initializes the case outcome controller.
     */
    function initController () {
      $scope = $rootScope.$new();
      $scope.case = { id: _.uniqueId() };

      $controller('civicaseExtrasCaseOutcomeController', { $scope: $scope });
      $rootScope.$digest();
    }
  });
})(CRM._, CRM.$);
