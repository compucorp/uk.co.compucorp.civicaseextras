/* eslint-env jasmine */

(function (_) {
  describe('CustomFields service', function () {
    var $q, $rootScope, crmApi, customFieldsMockData, CustomFields;

    beforeEach(module('civicaseextras', function ($provide) {
      crmApi = jasmine.createSpy('crmApi');

      $provide.value('crmApi', crmApi);
    }));

    beforeEach(inject(function (_$q_, _$rootScope_, CiviCaseExtrasCustomFields,
      _customFieldsMockData_) {
      $q = _$q_;
      $rootScope = _$rootScope_;
      CustomFields = CiviCaseExtrasCustomFields;
      customFieldsMockData = _customFieldsMockData_;
    }));

    describe('requesting the custom fields', function () {
      var expectedCustomFields, customFieldsResults;

      beforeEach(function () {
        var customFields = customFieldsMockData.get();
        expectedCustomFields = customFieldsMockData.getFieldsMap();

        crmApi.and.returnValue($q.resolve({
          count: customFields.length,
          values: customFields
        }));
      });

      describe('when the custom fields have not been loaded', function () {
        beforeEach(function (done) {
          CustomFields.get()
            .then(function (_customFieldsResults_) {
              customFieldsResults = _customFieldsResults_;
            })
            .finally(done);
          $rootScope.$digest();
        });

        it('requests the list of custom fields', function () {
          expect(crmApi).toHaveBeenCalledWith('CustomField', 'get', {
            'sequential': 1,
            'options': { 'limit': 0 },
            'return': [ 'label' ]
          });
        });

        it('returns a map of custom field labels indexed by their field name', function () {
          expect(customFieldsResults).toEqual(expectedCustomFields);
        });
      });

      describe('when the custom fields have already been loaded', function () {
        beforeEach(function (done) {
          CustomFields.get()
            .then(function () {
              return CustomFields.get();
            })
            .then(function (_customFieldsResults_) {
              customFieldsResults = _customFieldsResults_;
            })
            .finally(done);
          $rootScope.$digest();
        });

        it('requests the custom fields only once', function () {
          expect(crmApi.calls.count()).toBe(1);
        });

        it('returns a map of custom field labels indexed by their field name', function () {
          expect(customFieldsResults).toEqual(expectedCustomFields);
        });
      });
    });
  });
})(CRM._);
