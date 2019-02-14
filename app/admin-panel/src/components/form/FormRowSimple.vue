<template>

	<label class="c-FormRowText">
		<div class="Grid Grid--center Grid--gutter">
			<div class="Col-4 Col-12@SM">
				{{ fieldInfo.name }}<br>
				<small>{{ fieldInfo.desc }}</small><br>
			</div>

			<div class="Col-8 Col-12@SM">
				<input type="text" class="u-Width--full" :value="value"
					@input="$emit('input', $event.target.value)"
				>
			</div>
		</div>
	</label>

</template>


<script>

export default {
	props: ['fieldInfo', 'value'],
}

</script>
