<template>

	<div class="c-Content">
		<form @submit.prevent="saveContent">
			<div class="u-PageTop">
				<h1>Content</h1>

				<transition name="fade">
					<div class="u-PageTop__buttons" v-if="isFormReady">
						<label class="u-PageTop__option">
							Language:
							<select v-model="formValues['lang']">
								<option>EN</option>
							</select>
						</label>

						<button class="Btn Btn--gray" type="reset" @click.prevent="resetForm">Reset</button>
						<button class="Btn Btn--primary" type="submit">Save changes</button>
					</div>
				</transition>
			</div>

			<div class="c-Content__fields">
				<loader :is-hidden="isFormReady" />

				<transition-group name="fade">
					<div
						v-for="(fieldData, fieldId) in fieldsReceived"
						:key="fieldId"
					>
						<form-row-simple
							v-if="fieldData.type == 'simple'"
							v-model="formValues[fieldId]"
							:field-info="fieldData"
						/>
						<form-row-rich
							v-if="fieldData.type == 'rich'"
							v-model="formValues[fieldId]"
							:field-info="fieldData"
						/>
					</div>
				</transition-group>
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

	created() {
		axios.get('../admin-api/content')
			.then(result => {
				this.isFormReady = true;
				console.log(result.data);
				if (result.data.fields) {
					this.fieldsReceived = result.data.fields;
				}
			});
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

</style>