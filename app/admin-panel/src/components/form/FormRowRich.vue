<template>

	<label class="c-FormRowRich">
		<div class="Grid Grid--center Grid--gutter">
			<div class="Col-4 Col-12@SM">
				{{ fieldData.props.name }}<br>
				<small>{{ fieldData.props.desc }}</small><br>
			</div>

			<div class="Col-8 Col-12@SM">
				<textarea
					@input = "$emit('input', $event.target.value)"
					:value = "value"
					:name  = "fieldData.id"
					class  = "u-Width--full"
					type   = "text"
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