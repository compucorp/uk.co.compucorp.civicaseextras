(function (angular) {
  var module = angular.module('civicase');

  console.log('@', angular.module('civicase'));

  module.config(function ($provide) {
    $provide.decorator('formatCase', function ($delegate) {
      var originalFormatCase = $delegate[0];
      console.log('.', originalFormatCase);

      return function formatCaseFactory (formatActivity, ContactsDataService) {
        console.log('@', formatActivity, ContactsDataService, originalFormatCase);
      };
    });
  });
})(angular);
