<template>

	<div class="c-Home">
		<div class="c-Home__info">
			<div>
				<div class="u-Heading--hero">Hello</div>
				<h1>{{ $store.state.siteName }}</h1>
				<h2>Administration panel</h2>
			</div>
		</div>

		<div class="c-Home__bottom">
			<ul>
				<li>Aplication version: {{ $store.state.appVersion }}</li>
				<li>PHP version: {{ $store.state.phpVersion }}</li>
			</ul>

			<div class="c-Home__poweredby">
				Powered by
				<a href="https://github.com/peronczyk/VIZU/" target="_blank" title="Simple Landing Page Generator"><strong>VIZU</strong></a>
			</div>
		</div>
	</div>

</template>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Home {
	display: flex;
	flex-direction: column;

	&__info {
		display: flex;
		align-items: center;
		flex-grow: 1;
		width: 100%;
	}

	&__bottom {
		position: relative;
		display: flex;
		align-items: center;
		margin-top: auto;
		width: 100%;
		min-height: 50px;
		font-size: 13px;
		color: $color-gray;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: $gutter * -1.5;
			right: $gutter * -1.5;
			height: 1px;
			background-color: $color-lines;
		}

		ul {
			height: 100%;
			list-style-type: none;

			li {
				display: inline-block;
				margin-right: 20px;
			}
		}
	}

	&__poweredby {
		margin-left: auto;

		strong {
			display: block;
			color: $color-brand-3;
		}
	}
}

</style>