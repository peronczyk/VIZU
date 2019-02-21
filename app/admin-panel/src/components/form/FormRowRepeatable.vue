<template>

	<div class="c-FormRowRepeatable">
		<p>{{ fieldData.props.name }}</p>
		<ul>
			<li
				v-for = "num in count"
				:key  = "num"
			>
				<a @click.prevent="removeGroup(num)" class="c-FormRowRepeatable__remove"></a>
				<input type="text" v-for="(subField, subFieldNum) in fieldData.children" :key="subFieldNum">
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
			count: 4,
		}
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
		padding: 30px 20px;
		border: 1px solid $color-lines;

		&:last-child {
			display: flex;
			align-items: center;
			justify-content: center;
		}
	}

	&__remove {
		position: absolute;
		top: 0;
		right: 0;
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
