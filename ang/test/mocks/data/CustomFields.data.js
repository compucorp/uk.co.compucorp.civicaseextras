(function (_, angular) {
  var module = angular.module('civicaseextras');
  var customFieldsData = [
    { id: _.uniqueId(), name: 'Custom Field #1', label: 'Custom Field #1' },
    { id: _.uniqueId(), name: 'Custom Field #2', label: 'Custom Field #2' },
    { id: _.uniqueId(), name: 'Custom Field #3', label: 'Custom Field #3' }
  ];

  module.service('customFieldsMockData', function () {
    return {
      get: function () {
        return _.cloneDeep(customFieldsData);
      },

      getFieldsMap: function () {
        return _.indexBy(this.get(), function (customField) {
          return 'custom_' + customField.id;
        });
      }
    };
  });
})(CRM._, angular);
