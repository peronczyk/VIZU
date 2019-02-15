<template>

	<label class="c-FormRowText">
		<div class="Grid Grid--center Grid--gutter">
			<div class="Col-4 Col-12@SM">
				{{ fieldData.name }}<br>
				<small>{{ fieldData.desc }}</small><br>
			</div>

			<div class="Col-8 Col-12@SM">
				<input
					@input = "$emit('input', $event.target.value)"
					:value = "value"
					:name  = "fieldData.id"
					class  = "u-Width--full"
					type   = "text"
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
		this.value = this.fieldData.value || this.value;
	}
}

</script>
