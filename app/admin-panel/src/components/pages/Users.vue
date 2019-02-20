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
						<input
							v-model     = "passwordChangeData.currentPassword"
							type        = "password"
							name        = "password_current"
							placeholder = "Current password"
						>
					</label>
					<label>
						<input
							v-model     = "passwordChangeData.newPassword1"
							type        = "password"
							name        = "password_new_1"
							placeholder = "New password"
						>
					</label>
					<label>
						<input
							v-model     = "passwordChangeData.newPassword2"
							type        = "password"
							name        = "password_new_2"
							placeholder = "Confirm new password"
						>
					</label>
					<button type="submit">Send</button>
				</form>

				<h3>Add user</h3>
				<form method="post" @submit.prevent="addUserAction">
					<label>
						<input
							v-model     = "addUserData.email"
							type        = "email"
							name        = "email"
							placeholder = "Email address"
						>
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
import { mapActions } from 'vuex';

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
		...mapActions([
			'openToast',
		]),

		passwordChangeAction() {
			let formData = new FormData();
			axios.post('../admin-api/users/password-change', this.passwordChangeData)
				.then(receivedStatus => {
					console.info('passwordChangeAction done');
					console.log(receivedStatus);
				});
		},

		addUserAction() {
			let formData = new FormData();
			formData.append('email', this.addUserData.email)
			axios.post('../admin-api/users/add', formData)
				.then(result => {
					if (result.data.message) {
						this.openToast(result.data.message);
					}
				});
		},

		getUsersList() {
			axios.get('../admin-api/users/list')
				.then(result => {
					this.usersList = result.data['users-list'];
				});
		},
	},

	created() {
		this.getUsersList();
	},
}

</script>