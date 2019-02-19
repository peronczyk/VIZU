<template>

	<div>
		<div class="u-PageTop">
			<h1>History</h1>
		</div>

		<table>
			<thead>
				<tr>
					<th>Field name</th>
					<th>Change time</th>
				</tr>
			</thead>

			<tbody>
				<tr
					v-for="entry in historyData"
					:key="entry.id"
				>
					<td>{{ entry.id }}</td>
					<td>{{ entry.modified }}</td>
				</tr>
			</tbody>
		</table>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';

export default {
	data() {
		return {
			historyData: []
		};
	},

	methods: {
		getHistoryData() {
			axios.get('../admin-api/history/list')
				.then(result => {
					this.historyData = result.data['history'];
				});
		},
	},

	created() {
		this.getHistoryData();
	},
}

</script>