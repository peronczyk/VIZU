<template>

	<div>
		<div class="u-PageTop">
			<h1>Users</h1>
		</div>

		<div class="Grid Grid--gutter">
			<div class="Col-6 Col-12@MD">
				<div class="c-UserList">
					<h3>Users list</h3>
					<ul class="u-DashList">
						<li v-for="user in usersList" :key="user.email">{{ user.email }}</li>
					</ul>
				</div>
			</div>

			<div class="Col-6 Col-12@MD">
				<h3>Password change</h3>
				<p><small>For user: <strong>{{ user.email }}</strong></small></p>
				<form method="post" @submit.prevent="passwordChangeAction">
					<label>
						<input type="password" placeholder="Actual password" v-model="passwordChangeData.actualPassword">
					</label>
					<label>
						<input type="password" placeholder="New password" v-model="passwordChangeData.newPassword1">
					</label>
					<label>
						<input type="password" placeholder="Confirm new password" v-model="passwordChangeData.newPassword2">
					</label>
					<button type="submit">Send</button>
				</form>

				<h3>Add user</h3>
				<form method="post" @submit.prevent="addUserAction">
					<label>
						<input type="text" placeholder="Email address" v-model="addUserData.email">
					</label>
					<button type="submit">Send</button>
				</form>
			</div>
		</div>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';

export default {
	data() {
		return {
			user: {
				email: 'changeme@domain.com'
			},
			usersList: [],
			passwordChangeData: {},
			addUserData: {},
		};
	},

	methods: {
		passwordChangeAction() {
			axios.post('../admin-api/users/change_password', this.passwordChangeData)
				.then(receivedStatus => {
					console.info('passwordChangeAction done');
					console.log(receivedStatus);
				});
		},

		addUserAction() {
			axios.post('../admin-api/users/add', this.addUserData)
				.then(receivedStatus => {
					console.info('addUserAction done');
					console.log(receivedStatus);
				});
		}
	},

	created() {
		axios.get('../admin-api/users/list')
			.then(result => {
				this.usersList = result.data['users-list'];
			});
	},
}

</script>