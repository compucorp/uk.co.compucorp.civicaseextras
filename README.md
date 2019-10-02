# uk.co.compucorp.civicaseextras

This extension adds the following features related to cases:

* It displays outcome panels related to case activities.
* It adds a red warning to cases that have not been updated in a certain amount of time.

## Technical Requirements

* [CiviCase 1.0.0-alpha9](https://github.com/compucorp/uk.co.compucorp.civicase) or later.

## Installation

* Clone this repository into the `civicrm/ext` folder.
* Go to the extensions page `/civicrm/admin/extensions?reset=1`.
* Refresh the list of extensions.
* Install "CiviCase Extras".

## Usage

### Case Outcomes

#### Configure

1. Create a new Activity Type Category called "Outcome". The value must be "outcome":
`/civicrm/admin/options/activity_category?reset=1`
2. Assign the "Outcome" category to existing or new activity types:
`/civicrm/admin/options/activity_type?reset=1`
3. Update the activity status so they accept the outcome activity types.
`/civicrm/admin/options/activity_status?reset=1`. Without this change the activities
can't be created.
  * Edit some status, for example "Scheduled", "Completed", or "Available".
  * For the "Activity Category" field add the "Outcome" category.
  * Save.
4. Create new custom fields for outcomes:
`/civicrm/admin/custom/group?action=add&reset=1`
  * *Used for:* Select "Activities".
  * Select the activity types that have the "Outcome" category.
  * Add required fields

#### Final notes:

* There will be an outcome panel for each activity type with the "Outcome" category.
* The name of the outcome panel will be the activity type label.
* The custom field values will be shown inside the panels.

### Case last modified warning

This will be done automatically for cases last modified in the last 90 days, but
can be configured in the CiviCase settings:

`/civicrm/admin/setting/case?reset=1`.
