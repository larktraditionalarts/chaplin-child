jQuery(document).ready(function() {
	var workshopUrl = '/wp-json/wp/v2/workshop?per_page=100';
	var instructorUrl = '/wp-json/wp/v2/instructor?per_page=100';

	window.data = {
		workshops: [],
		camp: {},
		genre: {},
		workshop_tag: {},
		instrument: {},
		locations: {
			camp: 'workshop_camp[0].term_id',
			genre: 'genre',
			workshop_tag: 'workshop_tag',
			instrument: 'instrument',
			instructor: 'workshop_instructor[0].post_title',
			title: 'title.rendered',
		}
	};

	function find(workshop, thing, def) {
		return lodash.get(workshop, window.data.locations[thing], def);
	}

	function renderWorkshops(workshops) {
		var html = '<ul class="workshops-schedule">';
		for(var i = 0; i < workshops.length; i++) {
			var w = workshops[i];
			var instructor = window.data.instructors[w.workshop_instructor];
			var instructorName = lodash.get(instructor, 'title.rendered', 'TBA').replace(/"/g, "'").replace(/\n/g, '');
			var instructorBody = lodash.get(instructor, 'content.rendered', '').replace(/"/g, "'").replace(/\n/g, '');
			var instructorPic = lodash.get(instructor, 'pic.guid', '').replace(/(\.[a-z]+)$/i, '-150x150$1');
			var instructorYoutube = lodash.get(instructor, 'youtube', '');
			var instructorWebsite = lodash.get(instructor, 'website', '');
			var instructorMedia1 = lodash.get(instructor, 'media_1.guid', '');
			var instructorMedia2 = lodash.get(instructor, 'media_2.guid', '');
			var workshopBody = lodash.get(w, 'content.rendered', '').replace(/"/g, "'").replace(/\n/g, '');
			var workshopLocation = lodash.get(w, 'workshop_location[0].name', '').replace(/"/g, '\"').replace(/\n/g, '');
			var workshopCamp = lodash.get(w, 'workshop_camp[0].name', '').replace(/"/g, "'").replace(/\n/g, '');
			var workshopGenre = lodash.get(w, 'genre', []);
			var workshopInstruments = lodash.get(w, 'instrument', []);
			var workshopTags = lodash.get(w, 'workshop_tag', []);
			var workshopTitle = find(w, 'title', 'TBA').replace(/"/g, "'").replace(/\n/g, '');

			html += '<li class="workshop"';
			html += ' data-workshop-name="' + workshopTitle + '"';
			html += ' data-workshop-body="' + workshopBody + '"';
			html += ' data-instructor-body="' + instructorBody + '"';
			html += ' data-instructor-pic="' + instructorPic + '"';
			html += ' data-instructor-youtube="' + instructorYoutube + '"';
			html += ' data-instructor-website="' + instructorWebsite + '"';
			html += ' data-instructor-name="' + instructorName + '"';
			html += ' data-instructor-media1="' + instructorMedia1 + '"';
			html += ' data-instructor-media2="' + instructorMedia2 + '"';

			html += '>';
			html += '<div class="title">' + workshopTitle + '</div>';
			html += '<div class="instructor">with ' + instructorName + '</div>';
			html += '<div class="location">at ' + workshopLocation + ' in ' + workshopCamp + '</div>';

			// genre
			workshopGenre.forEach(function(g) {
				html += '<div class="pill genre">' + window.data.genre[g] + '</div>';
			});

			// instrument
			workshopInstruments.forEach(function(i) {
				html += '<span class="pill instrument">' + window.data.instrument[i] + '</span>';
			});

			// workshop_tag
			workshopTags.forEach(function(t) {
				html += '<span class="pill tag">' + window.data.workshop_tag[t] + '</span>';
			});

			html += '</li>';
		}
		html += '</ul>';

		jQuery('.lta-workshop-list').html(html);
		bindWorkshopsClickHandlers();

		// console.log(window.data);
	}


	jQuery('label.selectit').each(function () {
		var $this = jQuery(this);
		var label = jQuery.trim($this.text());
		var vals = $this.find('input').attr('id').split('-');
		var category = vals[1];
		var value = vals[2];

		window.data[category][value] = label;
	});

	jQuery('.lta-workshop-content').on('change', 'input', function() {
		var filteredWorkshops = window.data.workshops.slice();

		$('.lta-workshop-content .collapseomatic ~ .collapseomatic_content ').each(function() {
			var valuesToFilter = lodash.map(
				$(this).find('input:checked'),
				function (value) {
					var vals = value.id.split('-');

					return [vals[1], vals[2]];
				}
			);

			// console.log('valuesToFilter', valuesToFilter);

			if (!valuesToFilter.length) return;

			filteredWorkshops = lodash.filter(
				filteredWorkshops,
				function (w) {
					return lodash.filter(valuesToFilter, function (v) {
						var category = v[0];
						var value = v[1];
						var workshopValue = find(w, category, false);
						// console.log({ category, value, workshopValue });

						if (!workshopValue) return false;
						if (!jQuery.isArray(workshopValue)) return value == workshopValue;

						return lodash.filter(workshopValue, function (o) { return o == value; }).length > 0;
					}).length > 0;
				}
			);
		});

		renderWorkshops(filteredWorkshops);
	});

	jQuery('.lta-workshop-content').hide();
	getAllAsync(instructorUrl).then(function (instructorsArray) {
		// console.log(instructorsArray);

		var instructors = instructorsArray.reduce(
			function (acc, inst) {
				acc[inst.id] = inst;

				return acc;
			},
			{},
		);			
		
		window.data.instructors = instructors;

		return getAllAsync(workshopUrl);
	}).then(function (workshops) {
		jQuery('.lta-loading-image').hide();
		jQuery('.lta-workshop-content').show();
		window.data.workshops = workshops.sort(function (a, b) {
			var workshopTitleA = find(a, 'title', 'TBA').replace(/"/g, "'").replace(/\n/g, '');
			var workshopTitleB = find(b, 'title', 'TBA').replace(/"/g, "'").replace(/\n/g, '');

		  	return workshopTitleA.localeCompare(workshopTitleB);
		});			
		renderWorkshops(window.data.workshops);
	});
});
