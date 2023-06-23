/*---------------------------------Login & Registration Form JS---------------------------------------------------------*/
//Disable Submit
$('.register-submit').attr('disabled', 'disabled');

//View Login or Register form
$(".login-form").click(function () {
  $(".user-registration-form-section").css({ 'display': 'none' });
  $(".user-login-form-section ").css({ 'display': 'block' });
});

$(".register-form").click(function () {
  $(".user-login-form-section ").css({ 'display': 'none' });
  $(".user-registration-form-section").css({ 'display': 'block' });
});

//User Login Form Submission
$("#user_login_form").submit(function (event) {
  event.preventDefault();
  var email = $('#user-login-email').val();
  var password = $('#user-login-password').val();
  var login_nonce_check = $('#login-form-nonce').val();
  var delay = 500;
  $.ajax({
    type: "post",
    url: ajax_url[0],
    data: {
      action: 'login_submit_function',
      email: email,
      password: password,
      login_nonce_check: login_nonce_check
    },
    error: function (err) {
      console.log(err);
    },
    success: function (response) {
      setTimeout(function () {
        //console.log(response);
        window.location.replace(response);
      }, delay);
    },
  });
});

//Check After Login Form Submit Response from AJAX
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('login_check')) {
  $(".user-registration-form-section").css({ 'display': 'none' });
  $(".user-login-form-section ").css({ 'display': 'block' });
  $("body").prepend("<style>.user-login-form-section .form-elements-container .form-element .passowrd-check-err{display : block !important; }</style>");
}

//User Registration Form Submission 
$("#user_registration_form").submit(function (event) {
  event.preventDefault();
  var email = $('#user-email').val();
  var password = $('#user-password').val();
  var register_nonce_check = $('#register-form-nonce').val();
  var delay = 500;
  $.ajax({
    type: "post",
    url: ajax_url[0],
    data: {
      action: 'registration_submit_function',
      email: email,
      password: password,
      register_nonce_check: register_nonce_check
    },
    error: function (err) {
      console.log(err);
    },
    success: function (response) {
      setTimeout(function () {
        //console.log(response);
        window.location.replace(ajax_url[1] + "/urf-user-registration-bio-page/");
      }, delay);
    },
  });
});


//User Bio Form Submission
$("#user_registration_bio_form").submit(function (event) {
  event.preventDefault();
  var submit_date = $('#bio-submit-date').val();
  var name = $('#name').val();
  var address = $('#address').val();
  var phone = $('#phone').val();
  var about = $('#about').val();
  var occupation = $('#occupation').val();
  var experience = $('#years_of_experience').val();
  var education = $('#education').val();
  var register_bio_nonce_check = $('#register-bio-form-nonce').val();
  console.log(submit_date);
  $.ajax({
    type: "post",
    url: ajax_url[0],
    data: {
      action: 'registration_bio_form_submit_function',
      submit_date: submit_date,
      name: name,
      address: address,
      phone: phone,
      about: about,
      occupation: occupation,
      experience: experience,
      education: education,
      register_bio_nonce_check: register_bio_nonce_check
    },
    error: function (err) {
      console.log(err);
    },
    success: function (response) {
      //console.log(response);
      $('#user_registration_bio_form').remove();
      $('.user-registration-bio-section').fadeIn().append('<h2 style="text-align:center;">Thank you for your response. We will get in touch with you shortly.</h2>');
    },
  });
});

//User Registration Form Password Check
$("#user-re-password").keyup(function () {
  var password = $('#user-password').val();
  var re_password = $(this).val();
  if (password !== re_password) {
    $('.passowrd-check-err').css({ 'display': 'block' });
    $('.passowrd-check-ok').css({ 'display': 'none' });
  } else {
    $('.passowrd-check-err').css({ 'display': 'none' });
    $('.passowrd-check-ok').css({ 'display': 'block' });
    $('.register-submit').removeAttr('disabled');
  }
  //console.log(re_password);
});

/*---------------------------------Login & Registration Form JS---------------------------------------------------------*/