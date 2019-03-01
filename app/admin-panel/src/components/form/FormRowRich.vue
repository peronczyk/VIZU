<template>

	<div class="c-FormRowRich u-FormRow">
		<div class="u-FormRow__label">
			{{ fieldData.props.name }}<br>
			<small v-if="fieldData.props.desc">{{ fieldData.props.desc }}</small>
		</div>

		<div class="u-FormRow__input">
			<rte
				@input   = "$emit('input', $event)"
				:name    = "fieldData.props.id"
				:value   = "value"
			/>
		</div>
	</div>

</template>


<script>

// Components
import Rte from '../objects/Rte.vue';

export default {
	components: {
		Rte,
	},

	props: {
		fieldData: Object,
	},

	data() {
		return {
			value: '',
		};
	},

	created() {
		if (this.fieldData.value) {
			this.value = this.fieldData.value;
		}
	},
}

</script>