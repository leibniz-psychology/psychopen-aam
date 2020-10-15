document.querySelectorAll(".pa-link-query").forEach(itm => {
	let doi = itm.getAttribute('data-doi');
	let url = itm.getAttribute('data-url');
	fetch(url + '?doi=' + doi, {
		method: 'GET',
		//cache: "force-cache"
	}).then(function (response) {
		if (response.ok) {
			return response.json();
		} else
			throw new Error('Error getting API Data!');
	}).then((data) => {
		let link = data.content['paLink'];
		if (link !== undefined && link !== null && link !== '') {
			itm.querySelector('a').href = link;
			itm.style.display = 'block';
		}
	}).catch(error => {
		console.log(error);
	});
})
