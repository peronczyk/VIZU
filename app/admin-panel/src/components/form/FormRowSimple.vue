<template>

	<label class="c-FormRowText u-FormRow">
		<div class="u-FormRow__label">
			{{ fieldData.props.name }}<br>
			<small v-if="fieldData.props.desc">{{ fieldData.props.desc }}</small>
		</div>

		<div class="u-FormRow__input">
			<input
				v-model = "value"
				@input  = "$emit('input', $event.target.value)"
				:name   = "fieldData.props.id"
				type    = "text"
			>
		</div>
	</label>

</template>


<script>

export default {
	props: {
		fieldData: Object,
	},

	data() {
		return {
			value: '',
		}
	},

	created() {
		if (this.fieldData.value) {
			this.value = this.fieldData.value;
			this.$emit('input', this.value);
		}
	}
}

</script>
