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

function chpswhash(form, email, oldpassword, newpassword, confpassword) {
    if (  email.value == ''       || 
          oldpassword.value == ''     || 
          newpassword.value == ''  || 
          confpassword.value == ''      ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    
    if (newpassword.value.length < 4) {
        alert('Passwords must be at least 4 characters long. Please try again');
        form.newpassword.focus();
        return false;
    }
    
    var re = /(?=.*\d)(?=.*[a-zA-Z]).{4,}/;
    if (!re.test(newpassword.value)) {
        alert('Passwords must contain at least one number and one lowercase or uppercase letter.  Please try again');
        return false;
    }

    if (newpassword.value != confpassword.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.newpassword.focus();
        return false;
    }

    var op = document.createElement("input");
        form.appendChild(op);
        op.name = "op";
        op.type = "hidden";
        op.value = hex_sha512(oldpassword.value);
    
    var p = document.createElement("input");
        form.appendChild(p);
        p.name = "p";
        p.type = "hidden";
        p.value = hex_sha512(newpassword.value);

    // make sure the plaintext password doesn't get sent
    oldpassword.value = "";
    newpassword.value = "";
    confpassword.value = "";
    
    form.submit();
    return true;
}

function chusrnhash(form, email, newname) {
    
    if (  newname.value == ''  ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    
    if (  email.value == ''  ) {
        alert('Something went wrong. Please try again');
        return false;
    }
    
    re = /^\w+$/; 
    if(!re.test(form.newname.value)) { 
        alert("Username must contain only letters, numbers and underscores. Please try again"); 
        form.newname.focus();
        return false; 
    }
    
    form.submit();
    return true;
}

function forgothash(form, email) {
    
    if (  email.value == ''  ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    
    form.submit();
    return true;
}