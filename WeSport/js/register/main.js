function encryptPassword()
{
    document.forms['register'].elements['password'].value = CryptoJS.SHA512(document.getElementById("input_password").value);
}

var forms = document.querySelectorAll('.needs-validation')
// Loop over them and prevent submission
Array.prototype.slice.call(forms)
    .forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false);
    });

var form = document.forms["register"];
var pass = document.getElementById("input_password");
var passVerif = document.getElementById("input_password_verif");
var email = document.getElementById("input_email");
var emailVerif = document.getElementById("input_email_verif");
var datePicker = document.getElementById("input_birthday");

datePicker.max = new Date().toISOString().split("T")[0];

verifValues();

form.oninput = function ()
{
    verifValues();
}

function verifValues() {
    if(passVerif.value === "")
        passVerif.setCustomValidity("Veuillez confirmer votre mot de passe");
    else if(passVerif.value !== pass.value)
        passVerif.setCustomValidity("Les mot de passe ne correspondent pas");
    else
        passVerif.setCustomValidity("")
    passVerif.parentElement.querySelector('.invalid-feedback').innerText = passVerif.validationMessage;



    if(emailVerif.value === "")
        emailVerif.setCustomValidity("Veuillez confirmer votre adresse e-mail");
    else if(emailVerif.value !== email.value)
        emailVerif.setCustomValidity("Les adresses e-mail ne correspondent pas");
    else
        emailVerif.setCustomValidity("");
    emailVerif.parentElement.querySelector('.invalid-feedback').innerText = emailVerif.validationMessage;

    let differenceDate = new Date() - new Date(datePicker.value);
    if(differenceDate < 13 * 365 * 24 * 60 * 60 * 1000)
        datePicker.setCustomValidity("Vous devez avoir au moins 13 ans pour vous inscrire");
    else
        datePicker.setCustomValidity("");
    datePicker.parentElement.querySelector('.invalid-feedback').innerText = datePicker.validationMessage;
}