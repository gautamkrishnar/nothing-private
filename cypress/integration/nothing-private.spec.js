describe('Nothing Private e2e tests', function() {

    // Test initial API Calls on page load
    it('Initial page loading and API Calls', function() {
        cy.server();
        cy.route('GET', '*/safedb.php*').as('safeDbAPI');
        cy.visit('http://localhost:8080');
        cy.wait('@safeDbAPI');
        cy.get('@safeDbAPI').should((xhr) => {
            expect(xhr.status).to.eq(200);
            expect(xhr.url).contains('finger=').and.contains('check=1');
            expect(xhr.responseBody).to.have.property('status', 3);
        });
        cy.contains('Sorry to disappoint you, but you are wrong!. Everyone can track you. You can check it out for yourself. Just type your name below.');
    });

    // Tests save fingerprint functionality
    it('Functionality test: Typing on input box and clicking "see the magic" button', function() {
        cy.get('input#name').should('have.value', '');
        cy.get('input.btn').should('have.value', 'See the magic!');
        cy.server();
        cy.route('*/safedb.php*').as('safeDbAPI');
        cy.get('#name').type('cypress user').should('have.value', 'cypress user');
        cy.get('input.btn').click();
        cy.wait('@safeDbAPI');
        cy.get('@safeDbAPI').should((xhr) => {
            expect(xhr.status).to.eq(200);
            expect(xhr.url).contains('name=').and.contains('finger=');
            expect(xhr.responseBody).to.have.property('status', 1);
        });
    });

    // Tests get fingerprint functionality
    it('Functionality test: Refreshing the page, checks whether the fingerprint is detected', function() {
        cy.server();
        cy.route('GET', '*/safedb.php*').as('safeDbAPI');
        cy.visit('http://localhost:8080');
        cy.wait('@safeDbAPI');
        cy.get('@safeDbAPI').should((xhr) => {
            expect(xhr.status).to.eq(200);
            expect(xhr.url).contains('finger=').and.contains('check=1');
            expect(xhr.responseBody).to.have.property('name', 'cypress user');
            expect(xhr.responseBody).to.have.property('status', 0);
        });
        cy.contains('cypress user');
    });

    // Tests forget fingerprint functionality
    it('Functionality test: Clicking on "Forget me" button', function() {
        cy.server();
        cy.route('GET', '*/forgetme.php*').as('forgetMeAPI');
        cy.get('input.btn').should('have.value', 'Forget Me!');
        cy.get('input.btn').click();
        cy.wait('@forgetMeAPI').should((xhr) => {
            expect(xhr.status).to.eq(200);
            expect(xhr.url).contains('finger=');
            expect(xhr.responseBody).to.have.property('state', 1);
        });
    });
});