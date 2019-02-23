export default function(formValues) {
	let formData = new FormData();

	for (let id in formValues) {
		if (formValues[id] instanceof Object) {
			formData.append(id, JSON.stringify(formValues[id]));
		}
		else {
			formData.append(id, formValues[id]);
		}
	};

	console.log(formValues);


	return formData;
}