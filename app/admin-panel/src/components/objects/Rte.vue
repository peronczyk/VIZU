<template>

	<div
		:class="{'is-Focused': isFocused}"
		class="c-Rte"
	>
		<div class="c-Rte__buttons">
			<button tabindex="-1">B</button>
			<button tabindex="-1"><em>I</em></button>
			<button
				@click.prevent="toggleEditMode"
				:class="{'is-Active': editAsHtml}"
				tabindex="-1"
			>&lt;/&gt;</button>
		</div>

		<div class="c-Rte__wrapper">
			<div
				@input    = "contentChange"
				@focus    = "onFocus"
				@blur     = "onBlur"
				class     = "c-Rte__area"
				ref       = "contentEditable"
				:tabindex = "editAsHtml ? -1 : 0"
				contenteditable="true"
			></div>

			<textarea
				@input    = "textAreaChange"
				@focus    = "onFocus"
				@blur     = "onBlur"
				v-show    = "editAsHtml"
				v-model   = "content"
				:name     = "name"
				ref       = "textArea"
				class     = "c-Rte__field"
			></textarea>
		</div>
	</div>

</template>


<script>

export default {
	props: {
		name: String,
		value: String,
	},

	data() {
		return {
			content: '',
			isFocused: false,
			editAsHtml: false,
		}
	},

	methods: {
		contentChange(value) {
			this.content = value.target.innerHTML;
			this.$emit('input', this.content);
		},

		textAreaChange($event) {
			this.$refs.contentEditable.innerHTML = $event.target.value;
			this.$emit('input', this.content);
		},

		onFocus() {
			this.isFocused = true;
		},

		onBlur() {
			this.isFocused = false;
		},

		toggleEditMode() {
			this.editAsHtml = !this.editAsHtml;

			this.$nextTick(() => {
				(this.editAsHtml)
					? this.$refs.textArea.focus()
					: this.$refs.contentEditable.focus();
			});
		},
	},

	mounted() {
		if (this.value) {
			this.content = this.value;
			this.$refs.contentEditable.innerHTML = this.value;
			this.$emit('input', this.content);
		}
	},
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-Rte {
	$buttons-size: 40px;

	border: 1px solid $color-inputs;

	&.is-Focused {
		border-color: $color-blue;
	}

	&__buttons {
		display: flex;
		flex-wrap: wrap;
		min-height: $buttons-size;
		border-bottom: 1px solid $color-lines;
		opacity: .3;
		transition: .3s;

		.is-Focused & {
			opacity: 1;
		}

		button {
			height: $buttons-size;
			min-width: $buttons-size;
			background-color: transparent;
			font-size: 12px;
			font-weight: bold;

			&:hover {
				background-color: rgba($color-black, .05);
			}

			&:last-child {
				margin-left: auto;
			}

			&.is-Active {
				background-color: rgba($color-black, .1);
			}
		}
	}

	&__wrapper {
		position: relative;
	}

	&__area {
		min-height: 100px;
		max-height: 600px;
		padding: $input-padding;
		overflow: auto;
	}

	&__field {
		border: none;
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 100%;
		background-color: $color-light;
		font-family: monospace;
		resize: none;
	}
}

</style>
