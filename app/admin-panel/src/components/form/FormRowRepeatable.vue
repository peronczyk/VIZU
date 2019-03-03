<template>

	<div class="c-FormRowRepeatable">
		<p><strong>{{ fieldData.props.name }}:</strong></p>
		<ul class="c-FormRowRepeatable__list">
			<li
				v-for = "groupNum in value['groups-number']"
				:key  = "groupNum"
			>
				<a @click.prevent="removeGroup(groupNum - 1)" class="c-FormRowRepeatable__remove"></a>

				<strong>{{ groupNum }}</strong>

				<div
					v-for = "(subField, subFieldNum) in fieldData.children"
					:key  = "subFieldNum"
					class = "c-FormRowRepeatable__row"
				>
					{{ subField.props.name }}
					<small v-if="subField.props.desc">{{ subField.props.desc }}</small>

					<div class="c-FormRowRepeatable__row__field">
						<input
							@input  = "handleInputChange"
							v-if    = "subField.type == 'simple'"
							v-model = "value[subField.props.id + '__' + (groupNum - 1)]"
							:name   = "subField.props.id + (groupNum - 1)"
							type    = "text"
						>

						<rte
							@input  = "handleInputChange"
							v-if    = "subField.type == 'rich'"
							v-model = "value[subField.props.id + '__' + (groupNum - 1)]"
							:name   = "subField.props.id + (groupNum - 1)"
						/>
					</div>
				</div>
			</li>

			<li>
				<button
					@click.prevent="addGroup()"
					class="Btn Btn--small"
				>Add repeatable group</button>
			</li>
		</ul>
	</div>

</template>


<script>

// Components
import Rte from '../objects/Rte.vue';

export default {
	components: {
		Rte,
	},

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

		removeGroup(groupNumber) {
			for (let i = groupNumber; i <= this.value['groups-number']; i++) {
				this.fieldData.children.forEach(childField => {
					let actualKey   = childField.props.id + '__' + i;
					let previousKey = childField.props.id + '__' + (i - 1);

					if (i > groupNumber && this.value[actualKey]) {
						this.value[previousKey] = this.value[actualKey];
					}
					delete this.value[actualKey];
				});
			}

			this.value['groups-number']--;
			this.$emit('input', this.value);
		},

		handleInputChange() {
			this.$emit('input', this.value);
		},
	},

	created() {
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

	margin: #{$input-margin * 2} 0 #{$input-margin * 3} 0;

	&__list {
		display: flex;
		flex-wrap: wrap;
		margin-right: -2px;
		list-style-type: none;

		& > li {
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
	}

	&__row {
		margin-bottom: $input-margin;

		small {
			display: block;
		}

		&__field {
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
