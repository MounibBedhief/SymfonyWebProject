const login = document.querySelector('.form-box-login');
const register = document.querySelector('.form-box-register');
const loginLink = document.querySelector('.login_link');
const registerLink = document.querySelector('.register_link');

if (login && register && loginLink && registerLink) {
  loginLink.addEventListener('click', (event) => {
    event.preventDefault();
    register.style.display = 'none';
    login.style.display = 'block';
  });

  registerLink.addEventListener('click', (event) => {
    event.preventDefault();
    login.style.display = 'none';
    register.style.display = 'block';
  });
}
