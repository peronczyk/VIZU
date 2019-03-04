import './assets/styles/styles.scss';

import Vue from 'vue/dist/vue.esm.js';
import App from './App.vue';

import store from './store';
import router from './router.js';


// Global components
import Icon from './components/objects/Icon.vue';
Vue.component('icon', Icon);



/** --------------------------------------------------------------------------------
 * Initiate VUE instance
 */

window.admin = new Vue({
	el: '#app',
	store,
	router,
	template: '<app />',
	components: { App }
});