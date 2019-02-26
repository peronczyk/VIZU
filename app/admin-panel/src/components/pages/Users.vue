<template>

	<div>
		<div class="u-PageTop">
			<h1>Users</h1>
		</div>

		<div class="Grid Grid--gutter">
			<div class="Col-6 Col-12@MD">
				<div class="c-UserList">
					<h3>Users list</h3>
					<table>
						<thead>
							<th>Email</th>
							<th>Options</th>
						</thead>
						<tbody>
							<tr
								v-for="user in usersList"
								:key="user.id"
							>
								<td>{{ user.email }}</td>
								<td><a @click="deleteUser(user.id)">Delete</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="Col-6 Col-12@MD">
				<h3>Password change</h3>
				<form method="post" @submit.prevent="passwordChangeAction">
					<label>
						<input
							v-model     = "passwordChangeData['password_current']"
							type        = "password"
							name        = "password_current"
							placeholder = "Current password"
						>
					</label>
					<label>
						<input
							v-model     = "passwordChangeData['password_new_1']"
							type        = "password"
							name        = "password_new_1"
							placeholder = "New password"
						>
					</label>
					<label>
						<input
							v-model     = "passwordChangeData['password_new_2']"
							type        = "password"
							name        = "password_new_2"
							placeholder = "Confirm new password"
						>
					</label>
					<button type="submit">Change</button>
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
					<button type="submit">Add</button>
				</form>
			</div>
		</div>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';
import { mapActions } from 'vuex';
import prepareFormData from '../../vendor/PrepareFormData.js';

export default {
	data() {
		return {
			user: {},
			usersList: [],
			passwordChangeData: {},
			addUserData: {},
		};
	},

	methods: {
		...mapActions([
			'openToast',
			'userLogout',
		]),

		getUsersList() {
			axios.get('../admin-api/users/list')
				.then(result => {
					this.usersList = result.data['users-list'];
				});
		},

		passwordChangeAction() {
			axios.post('../admin-api/users/password-change', prepareFormData(this.passwordChangeData))
				.then(result => {
					if (result.data.message) {
						this.openToast(result.data.message);
					}
					if (result.data.success) {
						this.userLogout();
					}
				});
		},

		addUserAction() {
			axios.post('../admin-api/users/add', prepareFormData(this.addUserData))
				.then(result => {
					if (result.data.message) {
						this.openToast(result.data.message);
					}
					this.getUsersList();
				});
		},

		deleteUser(userId) {
			axios.get('../admin-api/users/delete?user-id=' + userId)
				.then(result => {
					this.openToast((result.data.success) ? 'User deleted' : 'User deletion failed');
					this.getUsersList();
				});
		},
	},

	created() {
		this.getUsersList();
	},
}

</script>