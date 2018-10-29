(function (_, angular) {
  var module = angular.module('civicaseextras');

  module.service('CiviCaseExtrasCustomFields', function ($q, crmApi) {
    var customFields;

    this.get = get;

    /**
     * Returns a map of custom fields. If the custom fields have already been loaded,
     * it returns the cached copy of the custom fields instead of making a new request.
     *
     * @return {Promise} resolves to an object.
     */
    function get () {
      return customFields
        ? $q.resolve(customFields)
        : loadCustomFields()
          .then(getIndexedCustomFields)
          .then(function (_customFields_) {
            customFields = _customFields_;

            return customFields;
          });
    }

    /**
     * Given a list of custom fields, it returns a map of them indexed by their
     * field name (Ex: custom_123: { id: 123, label: 'Custom Field Label' }).
     *
     * @param {Array} customFields a list of custom field objects
     * @return {Object} a map of custom field objects indexed by their field name.
     */
    function getIndexedCustomFields (customFields) {
      return _.indexBy(customFields, function (customField) {
        return 'custom_' + customField.id;
      });
    }

    /**
     * Requests all the custom fields and store them in a local variable in order
     * to cache the request and avoid making multiple api calls in the future.
     *
     * @return {Promise} resolves to an object that contains a the custom fields
     * indexed by their field name.
     */
    function loadCustomFields () {
      return crmApi('CustomField', 'get', {
        'sequential': 1,
        'options': { 'limit': 0 },
        'return': [ 'label' ]
      })
        .then(function (result) {
          return result.values;
        });
    }
  });
})(CRM._, angular);
