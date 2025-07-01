// cypress/e2e/login_spec.cy.js

describe('Login Page E2E Test', () => {

  // Before each test in this suite, visit the login page
  beforeEach(() => {
    cy.visit('http://localhost:180/z_cod_ot02/login.php');
  });

  it('should successfully log in with valid credentials', () => {
    // 1. Type the username into the first input box
    // We'll try to find the input by different selectors for robustness.
    // Replace with the actual selector for your input if these don't work.
    cy.get('input[name="email"]').should('be.visible').type('dmontoya1@test.com');
    // OR if it has an ID: cy.get('#username').should('be.visible').type('myuser@mail.com');
    // OR if it's the first text input: cy.get('input[type="text"]').first().should('be.visible').type('myuser@mail.com');

    // 2. Type the password into the second input box
    // Similar to username, replace with the actual selector.
    cy.get('input[name="password"]').should('be.visible').type('12345678');
    // OR if it has an ID: cy.get('#password').should('be.visible').type('1234567');
    // OR if it's the input with type password: cy.get('input[type="password"]').should('be.visible').type('1234567');

    // 3. Click the 'Login' button
    // Again, trying different common selectors. Pick the one that matches your button.
    cy.get('button[type="submit"]').should('be.visible').click();
    // OR if it has an ID: cy.get('#login-button').should('be.visible').click();
    // OR if it's a button with specific text: cy.contains('button', 'Login').should('be.visible').click();
    // OR if it's an input type submit: cy.get('input[type="submit"]').should('be.visible').click();


    // 4. (Optional but Recommended) Assert that the login was successful
    // This depends on what happens after a successful login.
    // Examples:
    // - Check if the URL changed to a dashboard or home page:
    cy.url().should('include', '/consola.php'); // Or whatever the success URL is
    // - Check for a specific element that only appears after login:
//    cy.contains('Welcome, myuser!').should('be.visible'); // Or a logout button, user profile link, etc.
    // - Check if a success message is displayed:
    // cy.contains('Login successful!').should('be.visible');
  });

  // You can add more test cases here, for example, for invalid login attempts
  it('should show an error with invalid credentials', () => {
    cy.get('input[name="username"]').type('wrong@mail.com');
    cy.get('input[name="password"]').type('wrongpassword');
    cy.get('button[type="submit"]').click();

    // Assert that an error message is displayed
    cy.contains('Invalid credentials').should('be.visible'); // Adjust text as per your app's error message
    // And that the URL remains on the login page or a specific error page
    cy.url().should('include', '/login.php');
  });

});