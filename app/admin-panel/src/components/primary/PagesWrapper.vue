<template>

	<div class="c-Pages">
		<transition name="page">
			<keep-alive>
				<router-view class="c-Pages__inner"></router-view>
			</keep-alive>
		</transition>
	</div>

</template>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Pages {
	position: relative;
	margin-left: 200px;
	min-height: 100vh;

	&__inner {
		padding: 0 $gutter * 1.5;
		width: 100%;
		min-height: 100vh;
		will-change: opacity, transform;
	}


	@include narrower-than(xl) {
		margin-left: 160px;
	}


	@include narrower-than(md) {
		margin-left: 60px;
	}
}

</style>
