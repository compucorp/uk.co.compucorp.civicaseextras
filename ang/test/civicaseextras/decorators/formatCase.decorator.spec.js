/* eslint-env jasmine */

(function (CRM) {
  describe('formatCase decorator', function () {
    var formatCase, formattedCase, mockCase;

    beforeEach(module('civicase', 'civicaseextras', 'civicase.data'));

    beforeEach(inject(function (CasesData, _formatCase_) {
      mockCase = CasesData.get().values[0];
      formatCase = _formatCase_;
    }));

    describe('when the case modified date is over the limit', function () {
      beforeEach(function () {
        CRM.civicaseextras.overdueNotificationLimit = 90;
        mockCase.modified_date = moment()
          .subtract(90, 'days')
          .format('YYYY-MM-DD HH:mm:ss');
        formattedCase = formatCase(mockCase);
      });

      it('marks the modified date as overdue', function () {
        expect(formattedCase.overdueDates.modified_date).toBe(true);
      });
    });

    describe('when the case modified date is not over the limit', function () {
      beforeEach(function () {
        CRM.civicaseextras.overdueNotificationLimit = 160;
        mockCase.modified_date = moment()
          .subtract(90, 'days')
          .format('YYYY-MM-DD HH:mm:ss');
        formattedCase = formatCase(mockCase);
      });

      it('does not mark the modified date as overdue', function () {
        expect(formattedCase.overdueDates.modified_date).toBe(false);
      });
    });
  });
})(CRM);
