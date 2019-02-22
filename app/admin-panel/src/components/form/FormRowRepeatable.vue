<template>

	<div class="c-FormRowRepeatable">
		<p><strong>{{ fieldData.props.name }}:</strong></p>
		<ul>
			<li
				v-for = "groupNum in fieldData['groups-number']"
				:key  = "groupNum"
			>
				<a @click.prevent="removeGroup(groupNum)" class="c-FormRowRepeatable__remove"></a>
				<label
					v-for = "(subField, subFieldNum) in fieldData.children"
					:key  = "subFieldNum"
				>
					{{ subField.props.name }}
					<small v-if="subField.props.desc">{{ subField.props.desc }}</small>
					<input type="text" :name="subField.props.id + groupNum">
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

	methods: {
		addGroup() {
			this.count++;
		},

		removeGroup() {
			this.count--;
		}
	},
}

</script>


<style lang="scss">

@import '../../assets/styles/definitions.scss';

.c-FormRowRepeatable {
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
		padding: 30px 20px 20px 20px;
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
		top: 4px;
		right: 4px;
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
