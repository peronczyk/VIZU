<template>

	<transition name="slide-right">
		<dialog v-if="isToastVisible" class="c-Toast">
			<p v-html="toastContent"></p>
		</dialog>
	</transition>

</template>


<script>

// Dependencies
import { mapActions, mapState } from 'vuex';

export default {
	computed: {
		...mapState([
			'isToastVisible',
			'toastContent',
		]),
	},

	methods: {
		...mapActions([
			'openToast',
			'closeToast'
		]),
	},
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Toast {
	position: fixed;
	z-index: +3;
	left: auto;
	bottom: $gutter;
	right: $gutter;
	display: flex;
	align-items: center;
	min-height: 50px;
	min-width: 160px;
	max-width: 400px;
	padding: 14px 20px;
	color: $color-white;
	background-color: $color-dark;
	box-shadow: $shadow-lg-medium;
	will-change: visibility, opacity, transform;

	a {
		color: $color-blue-light;
	}
}

</style>
