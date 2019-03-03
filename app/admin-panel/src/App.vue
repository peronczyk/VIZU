<template>

	<div class="c-App">
		<loader
			:is-hidden="isAppReady"
		/>

		<template v-if="isAppReady">
			<transition-group name="fade">
				<template v-if="userAccess > 0">
					<sidebar key="sidebar" />
					<pages-wrapper key="pagesWrapper" />
				</template>

				<auth-screen v-else key="authScreen" />
			</transition-group>
		</template>

		<toast />
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';
import { mapState, mapActions } from 'vuex';

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

	computed: {
		...mapState([
			'userAccess',
			'isAppReady',
		])
	},

	methods: {
		...mapActions([
			'fetchAppStatus',
		]),
	},

	created() {
		this.fetchAppStatus();
	},
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