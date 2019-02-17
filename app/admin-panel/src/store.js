import Vue from 'vue/dist/vue.esm.js';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		isAppReady: false,
		userAccess: 0,
		appVersion: null,
		phpVersion: null,
		siteName: null,
	},

	mutations: {
		appIsReady(state) {
			state.isAppReady = true;
		},

		setUserAccess(state, payload) {
			state.userAccess = payload ? payload : 0;
		},

		setAppVersion(state, payload) {
			state.appVersion = payload;
		},

		setPhpVersion(state, payload) {
			state.phpVersion = payload;
		},

		setSiteName(state, siteName) {
			state.siteName = siteName;
		}
	},

	actions: {
		userLogout({ commit }) {
			commit('setUserAccess', 0);

			this.axios.get('../admin-api/users/logout')
				.done(result => {
					if (result.data['user-access'] && result.data['user-access'] !== 0) {
						commit('setUserAccess', result.data['user-access']);
					}

					if (result.data.error) {
						console.log(result.data.error);
					}
				});
		}
	}
});