<span ng-if="!item.overdueDates[header.name]">
  {literal}
    {{ CRM.utils.formatDate(item[header.name]) }}
  {/literal}
</span>
<strong ng-if="item.overdueDates[header.name]"
  class="text-danger">
  {literal}
    {{ CRM.utils.formatDate(item[header.name]) }}
  {/literal}
  <i class="material-icons civicase__icon">error</i>
</strong>
