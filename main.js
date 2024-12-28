const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");
const menuBtnIcon = menuBtn.querySelector("i");

menuBtn.addEventListener("click", (e) => {
  navLinks.classList.toggle("open");

  const isOpen = navLinks.classList.contains("open");
  menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");
});

navLinks.addEventListener("click", (e) => {
  navLinks.classList.remove("open");
  menuBtnIcon.setAttribute("class", "ri-menu-line");
});

const scrollRevealOption = {
  distance: "50px",
  origin: "bottom",
  duration: 1000,
};

// header container
ScrollReveal().reveal(".header__container .section__subheader", {
  ...scrollRevealOption,
});

ScrollReveal().reveal(".header__container h1", {
  ...scrollRevealOption,
  delay: 500,
});

ScrollReveal().reveal(".header__container .btn", {
  ...scrollRevealOption,
  delay: 1000,
});

// room container
ScrollReveal().reveal(".room__card", {
  ...scrollRevealOption,
  interval: 500,
});

// feature container
ScrollReveal().reveal(".feature__card", {
  ...scrollRevealOption,
  interval: 500,
});

// news container
ScrollReveal().reveal(".news__card", {
  ...scrollRevealOption,
  interval: 500,
});


function toggleLoginForm() {
  var loginForm = document.getElementById("login-wrapper");
  if (loginForm.style.display === "none") {
      loginForm.style.display = "block";
  } else {
      loginForm.style.display = "none";
  }
}

// Add event listener to News link
document.getElementById("news-link").addEventListener("click", function(event) {
  event.preventDefault(); // Prevent default behavior of anchor tag
  toggleLoginForm();
});

// Add this JavaScript at the end of your body tag or in a separate .js file
document.getElementById("owner-signin-btn").addEventListener("click", function() {
  window.location.href = '/path-to-your-login-page'; // Replace with your actual login page URL
});

// javascript for add property
document.getElementById('file-upload').addEventListener('change', function(event) {
  // You can handle the file list here, such as displaying thumbnails or filenames
  // This is just a placeholder functionality to print out file names
  var files = event.target.files;
  var fileList = [];
  for (var i = 0; i < files.length; i++) {
    fileList.push(files[i].name);
  }
  console.log('Selected files:', fileList.join(', '));
});



// JavaScript to handle mouse hover over the .login-container
document.querySelector('.login-container').addEventListener('mouseenter', function() {
  this.classList.add('expanded');
});

document.querySelector('.login-container').addEventListener('mouseleave', function() {
  this.classList.remove('expanded');
});



//nav dropdown
document.addEventListener('DOMContentLoaded', function () {
  // Get the menu button and menu container elements
  var menuBtn = document.getElementById('menu-btn');
  var navLinks = document.querySelector('.nav__links');

  // Function to toggle the menu
  function toggleMenu() {
    navLinks.classList.toggle('active');
  }

  // Add click event to menu button
  menuBtn.addEventListener('click', toggleMenu);
});


  
document.addEventListener('DOMContentLoaded', function() {
  // Your code here
});


// JavaScript to handle form slide
     
// JavaScript to handle form slide
const loginText = document.querySelector(".title-text .login");
const loginForm = document.querySelector("form.login");
const loginBtn = document.querySelector("label.login");
const signupBtn = document.querySelector("label.signup");
const signupLink = document.querySelector("form .signup-link a");
const signUpButton = document.querySelector('.signUp_btn button');

// Check for hash in the URL on load to keep the form state
window.addEventListener('load', () => {
 if (window.location.hash === '#signup') {
   showSignupForm();
 }
});

// Show signup form without changing the page state
function showSignupForm() {
 loginForm.style.marginLeft = "-50%";
 loginText.style.marginLeft = "-50%";
}

// Set up click event for signup button
signupBtn.onclick = () => {
 window.location.hash = 'signup';
 showSignupForm();
};

// Set up click event for login button
loginBtn.onclick = () => {
 window.location.hash = 'login';
 loginForm.style.marginLeft = "0%";
 loginText.style.marginLeft = "0%";
};

// Set up click event for the link to the signup form
signupLink.onclick = () => {
 signupBtn.click();
 return false;
};

// Set up the form submission using AJAX
document.querySelector('.signup').addEventListener('submit', function(event) {
 event.preventDefault(); // Prevent traditional form submission

 let formData = new FormData(this);

 fetch('login.php', {
   method: 'POST',
   body: formData
 })
 .then(response => response.text())
 .then(data => {
   console.log(data);
   // Optionally do something with the response
 })
 .catch(error => {
   console.error('Error:', error);
 });

 // Keep the signup form visible
 showSignupForm();
});

// Ensure that the signup form stays visible after clicking the signup button
signUpButton.addEventListener('click', function() {
 window.location.hash = 'signup';
 showSignupForm();
});


