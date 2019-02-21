<template>
	<div class="c-FormRowWrapper">
		<form-row-repeatable
			v-if        = "fieldData.type == 'repeatable'"
			@input      = "$emit('input', $event)"
			:field-data = "fieldData"
		/>

		<form-row-rich
			v-if        = "fieldData.type == 'rich'"
			@input      = "$emit('input', $event)"
			:field-data = "fieldData"
		/>

		<form-row-simple
			v-if        = "fieldData.type == 'simple'"
			@input      = "$emit('input', $event)"
			:field-data = "fieldData"
		/>
	</div>
</template>


<script>

// Dependencies
import FormRowRepeatable from './FormRowRepeatable.vue';
import FormRowRich from './FormRowRich.vue';
import FormRowSimple from './FormRowSimple.vue';

export default {
	name: 'form-row-wrapper',

	components: {
		FormRowRich,
		FormRowSimple,
		FormRowRepeatable,
	},

	props: {
		fieldData: Object,
	},

	data() {
		return {
			rowValue: {},
		};
	},

	methods: {
		addRepeatableGroup(fieldId) {
			console.log(fieldId);
		},
	},
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-FormRowWrapper {
	&__repeatable {
		border-top: 1px solid $color-lines;
		border-bottom: 1px solid $color-lines;
		padding: 20px 0;
	}
}

</style>
