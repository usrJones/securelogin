/* Handles the hashing of the passwords for the login (formhash()) and registration (regformhash()) form */
// JavaScript hashes the passwd and passes it in the POST data by creating and populating a hidden field.

function formhash(form, password) {
    // create new element input, hashed password field
    var p = document.createElement("input");
 
    // add the new element to our form
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // makes sure the plaintext password doesn't get sent
    password.value = "";
 
    // finally submit the form
    form.submit();
}
 
function regformhash(form, uid, email, password, conf) {
     // check if each field has a value
    if (  uid.value == ''       || 
          email.value == ''     || 
          password.value == ''  || 
          conf.value == ''      ) {
 
        alert('You must provide all the requested details. Please try again');
        return false;
    }
 
    // check username
    re = /^\w+$/; 
    if(!re.test(form.username.value)) { 
        alert("Username must contain only letters, numbers and underscores. Please try again"); 
        form.username.focus();
        return false; 
    }
 
    // check that the password is min 4 chars
    // the check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (password.value.length < 4) {
        alert('Passwords must be at least 4 characters long.  Please try again');
        form.password.focus();
        return false;
    }
 
//    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; // at least one number, one lowercase and one uppercase letter // at least six characters 
    // at least one number, lower or uppercase letter, four characters
    var re = /(?=.*\d)(?=.*[a-zA-Z]).{4,}/;
    if (!re.test(password.value)) {
        alert('Passwords must contain at least one number and one lowercase or uppercase letter.  Please try again');
        return false;
    }
 
    // check password and confirmation are the same
    if (password.value != conf.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }
 
    // create a new element input, this will be our hashed password field
    var p = document.createElement("input");
 
    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // make sure the plaintext password doesn't get sent
    password.value = "";
    conf.value = "";
 
    // finally submit the form
    form.submit();
    return true;
}