<template>

	<div class="c-Auth">
		<div class="c-Auth__left">
			<div>
				<h1>Website</h1>
				<h2>Administration panel</h2>
			</div>
		</div>

		<div class="c-Auth__right">
			<form method="post" @submit.prevent="userLogin">
				<h4>Provide your credentials:</h4>

				<label>
					<input type="email" name="email" placeholder="Email address" v-model="formValues.email">
				</label>
				<label>
					<input type="password" name="password" placeholder="Password" v-model="formValues.password">
				</label>
				<button type="submit" class="u-Width--full">Login</button>
				<div class="c-Auth__message"></div>
			</form>
		</div>
	</div>

</template>


<script>

export default {
	data() {
		return {
			formValues: {}
		};
	},

	methods: {
		userLogin() {
			console.log(this.formValues);
			this.$xhr.post('/users/login', this.formValues)
				.done(receivedData => {
					console.log(receivedData);
					this.$store.commit('setUserAccess', receivedData['user-access']);
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

	form {
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
