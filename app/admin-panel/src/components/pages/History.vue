<template>

	<div>
		<div class="u-PageTop">
			<h1>History</h1>
		</div>

		<table>
			<thead>
				<tr>
					<th>Change type</th>
					<th>Field name</th>
					<th>Change time</th>
				</tr>
			</thead>

			<tbody>
				<tr v-for="entry in historyData" :key="entry.id">
					<td>{{ entry.type }}</td>
					<td>{{ entry.name }}</td>
					<td>{{ entry.time }}</td>
				</tr>
			</tbody>
		</table>
	</div>

</template>


<script>

export default {
	data() {
		return {
			historyData: []
		};
	}
}

</script>