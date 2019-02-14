<template>

	<div class="c-Content">
		<form @submit.prevent="saveContent" :class="{'is-FormReady': isFormReady}">
			<div class="u-PageTop">
				<h1>Content</h1>

				<div class="o-Top__buttons">
					Language:
					<select v-model="formValues['lang']">
						<option>EN</option>
					</select>

					<button class="Btn Btn--gray" type="reset" @click.prevent="resetForm">Reset</button>
					<button class="Btn Btn--primary" type="submit">Save changes</button>
				</div>
			</div>

			<div class="c-Content__fields">
				<loader :is-hidden="isFormReady" />

				<form-row-simple
					v-for="(fieldInfo, fieldId) in fieldsReceived" :key="fieldId"
					v-model="formValues[fieldId]"
					:fieldInfo="fieldInfo"
				/>
			</div>
		</form>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';

// Components
import FormRowSimple from '../form/FormRowSimple.vue';
import FormRowRich from '../form/FormRowRich.vue';
import Loader from '../objects/Loader.vue';

export default {
	components: {
		FormRowSimple,
		FormRowRich,
		Loader
	},

	data() {
		return {
			isFormReady: false,
			fieldsReceived: [],
			formValues: {},
		};
	},

	created() {
		axios.get('../admin-api/content')
			.then(result => {
				console.log(result.data);
				if (result.data.fields) {
					this.isFormReady = true;
					this.fieldsReceived = result.data.fields;
				}
			});
	},

	methods: {
		saveContent() {
			console.info('Save');
			console.log(this.formValues);

			axios.post('../admin-api/content', this.formValues)
				.then(result => {
					console.log(result.data);
				});
		},

		resetForm() {
			console.info('Reset');
			this.formValues = {};
			console.log(this.formValues);
		}
	},
}

</script>


<style lang="scss">

.c-Content {
	&__fields {
		position: relative;
		min-height: 200px;
	}
}

.o-Top__buttons {
	opacity: 0;
	transform: translateX(40px);

	.is-FormReady & {
		opacity: 1;
		transform: none;
	}
}

</style>