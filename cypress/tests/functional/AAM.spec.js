/* eslint-disable */
/* npx cypress open  --config integrationFolder=plugins/generic/psychopen-aam/cypress/tests */
describe('OpenID plugin tests', function () {

	it('Disable Forthcoming Articles Plugin', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('nav[class="app__nav"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// disable plugin if enabled
		cy.get('input[id^="select-cell-aamplugin-enabled"]')
			.then($btn => {
				if ($btn.attr('checked') === 'checked') {
					cy.get('input[id^="select-cell-aamplugin-enabled"]').click();
					cy.get('div[class*="pkp_modal_panel"] button[class*="pkpModalConfirmButton"]').click();
					cy.get('div:contains(\'The plugin "Forthcoming Articles" has been disabled.\')');
				}
			});
	});

	it('Enable Forthcoming Articles Plugin', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('nav[class="app__nav"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-aamplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Forthcoming Articles" has been enabled.\')');
	});

	it('Check AAM Page', function () {
		cy.visit('/index.php/publicknowledge/aam');
		cy.get('h1').contains('Forthcoming Articles');
	});
});
