<template>

	<div class="c-App">
		<loader
			:is-hidden="$store.state.isAppReady"
		/>

		<auth-screen
			v-if="$store.state.isAppReady && $store.state.userAccess < 1"
		/>

		<sidebar
			v-if="$store.state.isAppReady && $store.state.userAccess > 0"
		/>

		<pages-wrapper
			v-if="$store.state.isAppReady && $store.state.userAccess > 0"
		/>

		<toast />
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';

// Components
import Loader from './components/objects/Loader.vue';
import AuthScreen from './components/primary/AuthScreen.vue';
import PagesWrapper from './components/primary/PagesWrapper.vue';
import Sidebar from './components/primary/Sidebar.vue';
import Toast from './components/primary/Toast.vue';

export default {
	components: {
		AuthScreen,
		Loader,
		PagesWrapper,
		Sidebar,
		Toast
	},

	created() {
		this.$root.isContentLoading = true;
		axios.get('../admin-api/status/')
			.then(result => {
				this.$store.commit('appIsReady');
				this.$store.commit('setUserAccess', result.data['user-access']);
				this.$store.commit('setAppVersion', result.data['app-version']);
				this.$store.commit('setPhpVersion', result.data['php-version']);
				this.$store.commit('setSiteName', result.data['site-name']);
			});
	}
}

</script>


<style lang="scss">

.c-App {
	min-height: 100vh;
	overflow: hidden;

	& > .o-Loader {
		position: absolute;
		top: 50%;
		left: 50%;
	}
}

</style>