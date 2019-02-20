export default function(formValues) {
	let formData = new FormData();

	for (let id in formValues) {
		formData.append(id, formValues[id]);
	};

	return formData;
}