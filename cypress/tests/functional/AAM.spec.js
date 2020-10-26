/* npx cypress open  --config integrationFolder=plugins/generic/openid/cypress/tests */
describe('OpenID plugin tests', function () {

	it('Disable Forthcoming Articles Plugin', function () {
		cy.login(Cypress.env("ojs_username"), Cypress.env("ojs_password"), Cypress.env("context"));
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
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
		cy.login(Cypress.env("ojs_username"), Cypress.env("ojs_password"), Cypress.env("context"));
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-aamplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Forthcoming Articles" has been enabled.\')');
	});

	it('Check OpenID Authentication Plugin Login Page', function () {
		cy.visit('/index.php/' + Cypress.env("context") + '/aam');
		cy.get('h1').contains('Forthcoming Articles');
	});
});
