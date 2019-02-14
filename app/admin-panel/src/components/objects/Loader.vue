<template>

	<div class="c-Loader" :class="{'c-Loader--hidden': isHidden}"></div>

</template>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Loader {
	position: absolute;
	top: 50%;
	left: 50%;
	visibility: visible;
	transform: translate(-50%,-50%);
	transition: .3s;
	will-change: visibility, opacity, transform;

	&::before,
	&::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}

	// Rotating piece of circle
	&::before {
		border-width: 2px;
		border-style: solid;
		border-color: $color-blue transparent transparent transparent;
		animation: spin 1s linear infinite;
	}

	// Background circle
	&::after {
		border: 2px solid rgba($color-black, .1);
	}


	@include narrower-than(md) {
		width: 40px;
		height: 40px;
	}


	&--light {
		&::before {
			border-top-color: $color-white;
		}
	}

	&--hidden {
		visibility: hidden;
		opacity: 0;
		transform: translate(-50%,-50%) scale(.6);
	}
}

</style>


<script>

export default {
	props: ['isHidden']
}

</script>

