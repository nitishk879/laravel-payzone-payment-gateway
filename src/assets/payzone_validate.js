function validateInput(form,name){
  if (!!form.querySelector('[name='+name+']')){
    if(form.querySelector('[name='+name+']').value){
      return true;
    }
    else {
      return false;
    }
  }
  else
  {
    return false;
  }
}
function stripInvalidChars(form){
var elements = form.elements;
for (var i = 0, element; element = elements[i++];) {
    element.value = element.value.replace("'","");
}
}

/**
 * [validateForm Validates form and ensures all inputs are entered]
 * @method validateForm
 * @param  {[Object]}     cartForm
 * @param  {[String]}     intType
 * @param  {[String]}     [full=null]
 * @return {[Boolean]}     [False is any of the fields inputs are blank or missing]
 */
function validateForm(cartForm,intType,full){
  var form_errors = document.getElementById("form_errors");
    stripInvalidChars(cartForm);//add in a function to strip invalid chars on submit
  var errors =[];
  switch (intType) {
    case 'refund':
      validateInput(cartForm,'FullAmount') ? null : errors.push("Full Amount is missing");
      validateInput(cartForm,'OrderID') ? null : errors.push("Order ID is missing");
      validateInput(cartForm,'CrossReference') ? null : errors.push("Cross Reference is missing");
    break;
    case 'direct':

      if (full=="true"){
        var CrossRefTrans = (document.getElementById("CrossReferenceTransaction").value == 'false') ? 'false': 'true';
        if (CrossRefTrans=='false'){
        validateInput(cartForm,'CardNumber') ? null : errors.push("Card Number  is missing");
        validateInput(cartForm,'ExpiryDateYear') ? null : errors.push("Expiry Year is missing");
        validateInput(cartForm,'ExpiryDateMonth') ? null : errors.push("Expiry Month is missing");
        }
        validateInput(cartForm,'CV2') ? null : errors.push("CV2 is missing");
        validateInput(cartForm,'CountryCode') ? null : errors.push("Country Code is missing");
      }
      if (full=="false"){
        validateInput(cartForm,'Country') ? null : errors.push("Country is missing");
      }
      validateInput(cartForm,'FullAmount') ? null : errors.push("Amount is missing");
      validateInput(cartForm,'OrderID') ? null : errors.push("Order ID is missing");
      validateInput(cartForm,'OrderDescription') ? null : errors.push("Order Description is missing");
      validateInput(cartForm,'CustomerName') ? null : errors.push("Customer Name is missing");
      validateInput(cartForm,'Address1') ? null : errors.push("Address  is missing");
      validateInput(cartForm,'City') ? null : errors.push("City is missing");
      validateInput(cartForm,'State') ? null : errors.push("State is missing");
      validateInput(cartForm,'PostCode') ? null : errors.push("PostCode is missing");
      break;
    default:
  }
  if (errors.length>0){
    var error_warnings="<p class='error_title'>Errors have been found in the form</p>";
    for (error in errors){
      error_warnings += '<p>' + errors[error] + '</p>';
    }
    form_errors.innerHTML = error_warnings;
    return false;
  }
  else{
    return true;
  }
}
