// Dependencies
import Vue from 'vue/dist/vue.esm.js';
import VueRouter from 'vue-router';

// Pages
import Content from './components/pages/Content.vue';
import History from './components/pages/History.vue';
import Home from './components/pages/Home.vue';
import Users from './components/pages/Users.vue';

Vue.use(VueRouter);

const routes = [
	{
		path: '/',
		component: Home,
		name: 'Home',
		glyph: 'home'
	},
	{
		path: '/content',
		component: Content,
		name: 'Content',
		glyph: 'document'
	},
	{
		path: '/users',
		component: Users,
		name: 'Users',
		glyph: 'user'
	},
	{
		path: '/history',
		component: History,
		name: 'History',
		glyph: 'history'
	},
];

const router = new VueRouter({
	routes,
	mode: 'history'
});

export default router;