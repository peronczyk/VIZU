<template>

	<svg class="Icon" v-bind="{'class': 'Icon--' + size}">
		<use v-bind="{'xlink:href': '#icon-' + size + '_' + glyph}"></use>
	</svg>

</template>


<script>

export default {
	props: ['size', 'glyph']
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.Icon {
	display: inline-block;
	color: $color-dark;
	stroke-width: 1;
	stroke-linejoin: bevel;
	transition: .3s;

	&--24 {
		width: 24px;
		height: 24px;
	}
}

</style>