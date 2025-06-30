<template>
  <!-- Table to list the devices -->
  <div class="container mt_12 p-3 border rounded bg-light">
    <h4 class="text-center coman_main_heading mb-4">Device Details</h4>
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th colspan="2" class="text-primary">
            Device Information
          </th>
        </tr>
        <tr>
          <th>Title</th>
          <td>{{ device.device_title }}</td>
        </tr>
        <tr>
          <th>Device Type</th>
          <td>{{ device.device_type == 1 ? 'Personal' : 'Organizational' }}</td>
        </tr>
        <tr>
          <th>Device Model</th>
          <td>{{ device.device_model_name ?? '-' }}</td>
        </tr>
        <tr>
          <th>Serial Number/Asset Tag</th>
          <td>{{ device.serial_number ?? '-' }}</td>
        </tr>
        <tr>
          <th>IMEI</th>
          <td>{{ device.imei ?? '-' }}</td>
        </tr>
        <tr>
          <th>Device Cellular Service</th>
          <td>{{ device.cellular_service == 1 ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
          <th>Device Carrier</th>
          <td>{{ device.carrier ?? '-' }}</td>
        </tr>
        <tr>
          <th>Device Capacity</th>
          <td>{{ device.capacity ?? '-' }}</td>
        </tr>
        <tr>
          <th>Device Color</th>
          <td>{{ device.color ?? '-' }}</td>
        </tr>
        <tr>
          <th colspan="2" class="text-primary">
            User Information
          </th>
        </tr>
        <tr>
          <th>User Name</th>
          <td>{{ device.user_full_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 1">
          <th>Owner's First Name</th>
          <td>{{ device.org_user_first_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 1">
          <th>Owner's Last Name</th>
          <td>{{ device.org_user_last_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Owner's Full Name</th>
          <td>{{ device.device_owner_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th colspan="2" class="text-primary">
            Organization Information
          </th>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Organization Name</th>
          <td>{{ device.org_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Sub-Organization Name</th>
          <td>{{ device.sub_org_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Organization User Full Name</th>
          <td>{{ device.org_user_full_name ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>User Designation</th>
          <td>{{ device.org_user_designation ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Asset Tag</th>
          <td>{{ device.asset_tag ?? '-' }}</td>
        </tr>
        <tr v-if="device.device_type == 2">
          <th>Organization User ID</th>
          <td>{{ device.org_user_id ?? '-' }}</td>
        </tr>
        <tr>
          <th colspan="2" class="text-primary">
            Coverage Information
          </th>
        </tr>
        <tr>
          <th>Plan Name</th>
          <td>{{ device.plan_name }}</td>
        </tr>
        <tr>
          <th>Occurrence</th>
          <td>
            {{ $billingCycle[device.occurence - 1] || "-" }}
          </td>
        </tr>
        <tr>
          <th>Expiration Date</th>
          <td>{{ formatDate(device.expiration_date) ?? '-' }}</td>
        </tr>
        <tr>
          <th>Subscription Id</th>
          <td>{{ device.subscription_id ?? '-' }}</td>
        </tr>
        <tr>
          <th>Payment Added Date</th>
          <td>{{ formatDate(device.payment_added_date) ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
    <a href="/smarttiusadmin/devices/" class="btn customm_reset_btn mt-3 ml-2">Back</a>
  </div>
</template>
<script>
export default {
  props: {
    device: {
      type: Object,
      required: true
    }
  },
  methods : {
    /** Formate Date */
    formatDate(dateString) {
      return formatDateTimeAndTimeZone(dateString);
    },
  }
}
</script>