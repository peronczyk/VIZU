<template>

	<div class="c-Auth">
		<div class="c-Auth__left">
			<div>
				<h1><strong>{{ siteName || 'Website' }}</strong></h1>
				<h2>Administration panel</h2>
			</div>
		</div>

		<div class="c-Auth__right">
			<div>
				<form method="post" @submit.prevent="userLogin">
					<h4>Provide your credentials:</h4>

					<label>
						<input type="email" name="email" placeholder="Email address" v-model="formValues.email">
					</label>
					<label>
						<input type="password" name="password" placeholder="Password" v-model="formValues.password">
					</label>

					<transition name="fade">
						<div class="c-Auth__message" v-if="authMessage">{{ authMessage }}</div>
					</transition>

					<button type="submit" class="u-Width--full">Login</button>
				</form>

				<a @click.prevent="togglePasswordRecoveryBox">Password recovery</a>

				<transition name="fade">
					<div class="c-Auth__pwdrec" v-if="showPasswordRecoveryBox">
						<form method="post" @submit.prevent="passwordRecovery">
							<label>
								<input type="email" name="email" placeholder="Email" v-model="formValues.email">
							</label>

							<button type="submit">Recover</button>
						</form>
					</div>
				</transition>
			</div>

			<div class="c-Auth__logo">
				<a href="https://github.com/peronczyk/VIZU/" target="_blank" title="Simple Landing Page Generator">
					<svg><use xlink:href="#logo-vizu"></use></svg>
				</a>
			</div>
		</div>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';
import { mapState, mapActions } from 'vuex';
import prepareFormData from '../../vendor/PrepareFormData.js';

export default {
	data() {
		return {
			formValues: {},
			authMessage: null,
			showPasswordRecoveryBox: false,
		};
	},

	computed: {
		...mapState(['siteName']),
	},

	methods: {
		...mapActions([
			'openToast',
		]),

		userLogin() {
			axios.post('../admin-api/users/login', prepareFormData(this.formValues))
				.then(result => {
					this.$store.commit('setUserAccess', result.data['user-access'] || 0);
					this.authMessage = result.data.message || result.data.error.message || null;
				})
				.catch(error => {
					let response = error.response || {};
					let headers  = response.headers || {};

					this.openToast('<strong>Status: ' + response.status + '</strong><br>' + (headers['vizu-error-msg'] || 'Unknown error occured. Please contact administrators.'));

					console.log(response);
				});
		},

		togglePasswordRecoveryBox() {
			this.showPasswordRecoveryBox = !this.showPasswordRecoveryBox;
		},

		passwordRecovery() {
			axios.post('../admin-api/users/password-recovery', prepareFormData(this.formValues))
				.then(result => {
					this.openToast(result.data.message);
				});
		},
	}
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Auth {
	display: flex;
	min-height: 100vh;

	&__left,
	&__right {
		display: flex;
		align-items: center;
		padding: $gutter * 3;
		width: 50%;
		min-height: 100%;

		& > * {
			width: 100%;
		}
	}

	&__left {
		background-color: $color-white;
		box-shadow: $shadow-lg-light;
	}

	&__message {
		margin: 20px 0;
		color: $color-red;
	}

	&__pwdrec {
		padding-top: 20px;
	}

	&__logo {
		position: absolute;
		top: $gutter;
		left: $gutter;
		width: auto;

		svg {
			width: 60px;
			height: 60px;
		}
	}

	form {
		margin-bottom: 20px;
		max-width: 330px;
	}

	button {
		justify-content: center;
	}

	input {
		width: 100%;
	}
}

</style>
