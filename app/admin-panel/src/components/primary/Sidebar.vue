<template>

	<div class="c-Sidebar">
		<div class="c-Sidebar__logo">
			<svg><use xlink:href="#logo-vizu"></use></svg>
		</div>

		<nav class="c-Sidebar__nav">
			<router-link v-for="route in this.$router.options.routes" :to="route.path" :key="route.name">
				<icon size="24" :glyph="route.glyph" />
				{{route.name}}
			</router-link>
		</nav>

		<div class="c-Sidebar__bottom">
			<a href="../" title="Back to the website">
				<icon size="24" glyph="devices-mobile" />
			</a>

			<a @click="userLogout">Logout</a>
		</div>
	</div>

</template>


<script>

import { mapActions } from 'vuex';

export default {
	methods: {
		...mapActions([
			'userLogout'
		]),
	},
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

 .c-Sidebar {
	position: fixed;
	display: flex;
	flex-direction: column;
	top: 0;
	bottom: 0;
	left: 0;
	width: 200px;
	background-color: $color-white;
	box-shadow: $shadow-lg-light;
	overflow: auto;

	&__logo {
		display: flex;
		align-items: center;
		padding: 20px 0;
		justify-content: center;
		height: 16vh;

		svg {
			height: 50%;
		}

		.u-Color {
			&--dark {
				fill: $color-dark;
			}
			&--blue {
				fill: $color-blue;
			}
		}
	}

	&__nav {
		margin-bottom: 20px;
		width: 100%;

		a {
			position: relative;
			display: flex;
			align-items: center;
			padding: 0 #{$gutter * .6};
			height: 8vh;
			min-height: 40px;
			font-size: 15px;
			color: inherit;
			transition: .2s;

			&::after {
				content: '';
				position: absolute;
				top: 0;
				bottom: 0;
				right: 0;
				width: 0;
				background-color: $color-blue;
				opacity: 0;
				transition: .2s;
				will-change: opacity, width;
			}

			&:hover {
				&::after {
					opacity: 1;
					width: 2px;
				}
			}

			&:active {
				&::after {
					opacity: 1;
					width: 4px;
				}
			}

			.Icon {
				margin-right: 14px;
				opacity: .4;
			}
		}

		.router-link-exact-active {
			&::after {
				opacity: 1;
				width: 2px;
			}
		}
	}

	&__bottom {
		margin-top: auto;
		display: flex;
		width: 100%;
		min-height: 50px;
		border-top: 1px solid $color-lines;

		a {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 50%;
			min-height: 100%;
			border-right: 1px solid $color-lines;

			&:last-child { border: none; }

			.Icon { opacity: .4; }
		}
	}


	@include narrower-than(xl) {
		width: 160px;
	}


	@include narrower-than(md) {
		width: 60px;
	}
}

</style>
