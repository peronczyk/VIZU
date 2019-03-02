<template>

	<div
		:class="{'is-Focused': isFocused}"
		class="c-Rte"
	>
		<div class="c-Rte__buttons">
			<button
				@click.prevent="execCommand('bold')"
				tabindex="-1"
				title="Bold selection"
			>B</button>

			<button
				@click.prevent="execCommand('italic')"
				tabindex="-1"
				title="Italic selection"
			><em>I</em></button>

			<button
				@click.prevent="execCommand('insertUnorderedList')"
				tabindex="-1"
				title="Unordered list"
			>&bull;</button>

			<button
				@click.prevent="execCommand('insertOrderedList')"
				tabindex="-1"
				title="Ordered list"
			>1.</button>

			<div class="c-Rte__buttons__select">
				<span>Styles:</span>
				<ul>
					<li v-for="size in 6" :key="'heading' + size"><button
						@click.prevent="execCommand('formatBlock', '<h' + size + '>')"
						tabindex="-1"
					>Heading {{ size }}</button></li>

					<li><button
						@click.prevent="execCommand('formatBlock', '<p>')"
						tabindex="-1"
					>Paragraph</button></li>

					<li><button
						@click.prevent="execCommand('formatBlock', '<pre>')"
						tabindex="-1"
					>Preformatted</button></li>
				</ul>
			</div>

			<button
				@click.prevent="toggleEditMode"
				:class="{'is-Active': editAsHtml}"
				tabindex="-1"
				title="Toggle HTML edit mode"
			>&lt;/&gt;</button>
		</div>

		<div class="c-Rte__wrapper">
			<div
				@input    = "contentChange"
				@focus    = "onFocus"
				@blur     = "onBlur"
				class     = "c-Rte__content"
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
				class     = "c-Rte__textarea"
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
			this.$nextTick(() => {
				this.isFocused = false;
			});
		},

		focusCurrentElement() {
			this.$nextTick(() => {
				(this.editAsHtml)
					? this.$refs.textArea.focus()
					: this.$refs.contentEditable.focus();
			});
		},

		toggleEditMode() {
			this.editAsHtml = !this.editAsHtml;
			this.focusCurrentElement();
		},

		execCommand(command, value = null) {
			if (!this.isFocused) {
				this.focusCurrentElement();
			}

			console.log(command + ' / ' + value);
			document.execCommand(command, false, value);
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

	border: 1px solid $color-input-borders;

	&.is-Focused {
		border-color: $color-blue;
	}

	&__buttons {
		display: flex;
		flex-wrap: wrap;
		min-height: $buttons-size;
		border-bottom: 1px solid $color-lines;

		button {
			height: $buttons-size;
			min-width: $buttons-size;
			background-color: transparent;
			font-size: 12px;
			font-weight: bold;

			&:hover {
				background-color: rgba($color-black, .05);
			}

			&.is-Active {
				background-color: rgba($color-black, .1);
			}
		}

		&__select {
			position: relative;

			span {
				display: flex;
				align-items: center;
				height: $buttons-size;
				padding: $input-padding;
				cursor: pointer;
			}

			&:hover {
				span {
					background-color: rgba($color-black, .05);
				}

				ul {
					visibility: visible;
					opacity: 1;
					transform: scaleY(1);
				}
			}

			ul {
				visibility: hidden;
				opacity: 0;
				position: absolute;
				z-index: +2;
				top: 100%;
				left: 0;
				min-width: 140px;
				list-style-type: none;
				background-color: $color-white;
				box-shadow: 0 6px 20px rgba($color-black, .1);
				transform-origin: top;
				transform: scaleY(.95);
				transition: .3s;
				will-change: visibility, opacity, transform;
			}

			button {
				padding: 0 10px;
				width: 100%;
				text-align: left;
				white-space: nowrap;
				font-weight: normal;
			}
		}
	}

	&__wrapper {
		position: relative;
	}

	&__textarea,
	&__content {
		line-height: 1.4em;
	}

	&__content {
		min-height: 100px;
		max-height: 600px;
		padding: $input-padding;
		overflow: auto;
		color: $color-inputs;

		ul,
		ol {
			margin-bottom: .5em;
			padding-left: 20px;
		}
	}

	&__textarea {
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
