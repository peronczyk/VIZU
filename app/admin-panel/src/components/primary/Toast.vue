<template>

	<dialog class="c-Toast" :class="{'is-Visible': isToastVisible}">{{ dialogText }}</dialog>

</template>


<script>

export default {
	data() {
		return {
			isToastVisible: true,
			dialogText: 'Trolololo'
		}
	}
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Toast {
	visibility: hidden;
	position: fixed;
	z-index: +3;
	top: $gutter;
	right: $gutter;
	min-height: 50px;
	min-width: 50px;
	background-color: $color-dark;
	opacity: 0;
	transform: translateX(40px);
	will-change: visibility, opacity, transform;

	&.is-Visible {
		visibility: visible;
		opacity: 1;
		transform: none;
	}
}

</style>
