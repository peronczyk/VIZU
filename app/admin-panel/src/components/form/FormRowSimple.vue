<template>

	<label class="c-FormRowText">
		<div class="Grid Grid--center Grid--gutter">
			<div class="Col-4 Col-12@SM">
				{{ fieldData.props.name }}<br>
				<small v-if="fieldData.props.desc">{{ fieldData.props.desc }}</small>
			</div>

			<div class="Col-8 Col-12@SM">
				<input
					v-model = "value"
					@input  = "$emit('input', $event.target.value)"
					:name   = "fieldData.props.id"
					type    = "text"
				>
			</div>
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
