<template>
	<div class="c-FormRowWrapper">
		<form-row-simple
			v-if="fieldData.type == 'simple'"
			v-model="rowValue"
			:field-data="fieldData"
		/>

		<form-row-rich
			v-if="fieldData.type == 'rich'"
			v-model="rowValue"
			:field-data="fieldData"
		/>

		<div
			v-if="fieldData.type == 'repeatable'"
			class="c-FormRowWrapper__repeatable"
		>
			<form-row-wrapper
				v-for="(childFieldData, fieldId) in fieldData.children"
				:key="fieldId"
				:field-data="childFieldData"
			/>

			<button class="Btn Btn--small" @click.prevent="addRepeatableGroup(fieldData.id)">Add repeatable group</button>
		</div>
	</div>
</template>


<script>

// Dependencies
import FormRowRich from './FormRowRich.vue';
import FormRowSimple from './FormRowSimple.vue';
import FormRowWrapper from './FormRowWrapper.vue';

export default {
	name: 'form-row-wrapper',

	components: {
		FormRowRich,
		FormRowSimple,
		FormRowWrapper,
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
		}
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
