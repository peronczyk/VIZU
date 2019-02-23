<template>

	<div class="c-FormRowRepeatable">
		<p><strong>{{ fieldData.props.name }}:</strong></p>
		<ul>
			<li
				v-for = "groupNum in value['groups-number']"
				:key  = "groupNum"
			>
				<a @click.prevent="removeGroup(groupNum - 1)" class="c-FormRowRepeatable__remove"></a>
				<strong>{{ groupNum }}</strong>
				<label
					v-for = "(subField, subFieldNum) in fieldData.children"
					:key  = "subFieldNum"
				>
					{{ subField.props.name }}
					<small v-if="subField.props.desc">{{ subField.props.desc }}</small>
					<input
						@input  = "handleInputChange"
						v-model = "value[subField.props.id + '__' + (groupNum - 1)]"
						:name   = "subField.props.id + (groupNum - 1)"
						type    = "text"
					>
				</label>
			</li>
			<li>
				<button class="Btn Btn--small" @click.prevent="addGroup()">Add repeatable group</button>
			</li>
		</ul>
	</div>

</template>


<script>

export default {
	props: {
		fieldData: Object,
	},

	data() {
		return {
			value: {
				'groups-number': 0,
			},
		}
	},

	methods: {
		addGroup() {
			this.value['groups-number']++;
			this.$emit('input', this.value);
		},

		removeGroup(groupNum) {
			this.fieldData.children.forEach(childField => {
				delete this.value[childField.props.id + '__' + groupNum];
			});
			this.value['groups-number']--;
			this.$emit('input', this.value);
		},

		handleInputChange() {
			this.$emit('input', this.value);
		},
	},

	created() {
		if (this.fieldData['groups-number']) {
			this.value['groups-number'] = this.fieldData['groups-number'];
		}

		if (this.fieldData.value) {
			this.value = this.fieldData.value;
			this.$emit('input', this.value);
		}
	}
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-FormRowRepeatable {
	$repeatable-elem-padding: 20px;

	ul {
		display: flex;
		flex-wrap: wrap;
		margin-right: -2px;
		list-style-type: none;
	}

	li {
		position: relative;
		width: 33.3%;
		min-height: 80px;
		margin: -1px 0 0 -1px;
		padding: $repeatable-elem-padding;
		border: 1px solid $color-lines;

		&:last-child {
			display: flex;
			align-items: center;
			justify-content: center;
		}
	}

	label {
		small {
			display: block;
		}

		input {
			margin-top: 6px;
		}
	}

	&__remove {
		position: absolute;
		top: 6px;
		right: 6px;
		width: 28px;
		height: 28px;
		transition: .2s;

		&::before,
		&::after {
			content: '';
			position: absolute;
			top: 50%;
			left: 50%;
			width: 1px;
			height: 12px;
			background-color: $color-blue;
		}

		&::before {
			transform: translateY(-50%) rotate(45deg);
		}

		&::after {
			transform: translateY(-50%) rotate(-45deg);
		}

		&:hover {
			background-color: $color-blue-light;
		}
	}
}

</style>
