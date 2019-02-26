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
		isToastVisible: false,
		toastContent: null,
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
		fetchAppStatus({ commit, dispatch }) {
			axios.get('../admin-api/status/')
				.then(result => {
					commit('appIsReady');
					commit('setUserAccess', result.data['user-access']);
					commit('setAppVersion', result.data['app-version']);
					commit('setPhpVersion', result.data['php-version']);
					commit('setSiteName', result.data['site-name']);
				})
				.catch(error => {
					dispatch('openToast', 'Failed to fatch admin status');
				});
		},

		userLogout({ commit }) {
			commit('setUserAccess', 0);

			axios.get('../admin-api/users/logout')
				.then(result => {
					if (result.data['user-access'] && result.data['user-access'] !== 0) {
						commit('setUserAccess', result.data['user-access']);
					}

					if (result.data.error) {
						console.log(result.data.error);
					}
				});
		},

		openToast({ state, dispatch }, content) {
			state.isToastVisible = true;
			state.toastContent = content;

			setTimeout(() => {
				dispatch('closeToast');
			}, 12000);
		},

		closeToast({ state }) {
			state.isToastVisible = false;

			setTimeout(() => {
				state.toastContent = null;
			}, 500);
		},
	}
});