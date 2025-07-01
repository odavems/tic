describe('Login Page E2E Test', () => {
  //visita la pagina de login
  beforeEach(() => {
    cy.visit('http://localhost:180/z_cod_ot02/login.php');
  });

  it('debe logear correctamente validando las credenciales', () => {
   cy.get('input[name="email"]').should('be.visible').type('dmontoya1@test.com');
    cy.get('input[name="password"]').should('be.visible').type('12345678');
    cy.get('button[type="submit"]').should('be.visible').click();

    // Verifica si se redirige a la pagina de consola 
    cy.url().should('include', '/consola.php'); 
  });
});
