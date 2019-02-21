<template>

	<label class="c-FormRowRich">
		<div class="Grid Grid--center Grid--gutter">
			<div class="Col-4 Col-12@SM">
				{{ fieldData.props.name }}<br>
				<small>{{ fieldData.props.desc }}</small><br>
			</div>

			<div class="Col-8 Col-12@SM">
				<textarea
					v-model = "value"
					@input  = "$emit('input', $event.target.value)"
					:name   = "fieldData.id"
					type    = "text"
				></textarea>
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