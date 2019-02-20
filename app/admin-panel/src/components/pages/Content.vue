<template>

	<div class="c-Content">
		<form @submit.prevent="saveContent">
			<div class="u-PageTop">
				<h1>Content</h1>

				<transition name="fade">
					<div class="u-PageTop__buttons" v-if="isFormReady">
						<label class="u-PageTop__option">
							Language:
							<select v-model="activeLanguage">
								<option
									v-for     = "language in languages"
									:key      = "language.code"
									:value    = "language.code"
									:selected = "language.code == activeLanguage"
								>{{ language['short_name'] }}</option>
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
					<form-row-wrapper
						v-for       = "fieldData in fieldsReceived"
						v-model     = "formValues[fieldData.props.id]"
						:key        = "fieldData.props.id"
						:field-data = "fieldData"
					/>
				</transition-group>
			</div>
		</form>
	</div>

</template>


<script>

// Dependencies
import axios from 'axios';
import { mapActions } from 'vuex';

// Components
import FormRowSimple from '../form/FormRowSimple.vue';
import FormRowRich from '../form/FormRowRich.vue';
import FormRowWrapper from '../form/FormRowWrapper.vue';
import Loader from '../objects/Loader.vue';

export default {
	components: {
		FormRowSimple,
		FormRowRich,
		FormRowWrapper,
		Loader
	},

	data() {
		return {
			isFormReady: false,
			fieldsReceived: [],
			formValues: {},
			languages: [],
			activeLanguage: null,
		};
	},

	methods: {
		...mapActions([
			'openToast'
		]),

		saveContent() {
			let formData = new FormData();

			for (let id in this.formValues) {
				formData.append(id, this.formValues[id]);
			};

			axios.post('../admin-api/content/save?language=' + (this.activeLanguage || ''), formData)
				.then(result => {
					if (result.data.message) {
						this.openToast(result.data.message);
					}
				});
		},

		resetForm() {
			this.fieldsReceived = [];
			this.formValues = {};
		},

		fetchContent() {
			axios.get('../admin-api/content/list?language=' + (this.activeLanguage || ''))
				.then(result => {
					this.isFormReady = true;
					if (result.data.fields) {
						this.fieldsReceived = result.data.fields;
						this.languages = result.data.languages;
						this.activeLanguage = result.data['active-language'];
					}
				});
		}
	},

	created() {
		this.fetchContent();
	},

	watch: {
		activeLanguage(value, prevValue) {
			if (prevValue != null) {
				this.resetForm();
				this.fetchContent();
			}
		},
	}
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