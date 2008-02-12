<!-- BEGIN #

function CheckUncheckAll(the_form) 
{
    for (var i=0; i < the_form.elements.length; i++) 
    {
	    if (the_form.elements[i].type=="checkbox") 
	    {
	        the_form.elements[i].checked = !(the_form.elements[i].checked);
	    }
    }
}


function PopupSendSms(ta, tg)
{
    var pv = "PV";
    if (ta == pv)
    {
	    var url = "menu.php?inc=send_sms&op=sendsmstopv&dst_p_num="+tg;
    }
    else
    {
	    var url = "menu.php?inc=send_sms&op=sendsmstogr&dst_gp_code="+tg;
    }
    newwin=window.open("","WinSendSms","scrollbars","resizable=yes")
    newwin.moveTo(20,100)
    newwin.resizeTo(500,500)
    newwin.location=url	    
}

function ConfirmURL(inputText, inputURL)
{ 
    if (confirm(inputText)) document.location=inputURL
}


// this function needs to be a method of a form input object
// (input text, textarea, etc) that has had wireupSmsCountUpdate
// call on it
// 
function updateSmsCounts()
{
    var len= this.value.length;

    // get the current message length
    // and update the remaining-chars count.
    // if we're over the max, then 
    // truncate down to the max
    //
    var smsLenLeft = SMS_MAXCHARS  - len;
    if (smsLenLeft >= 0) {
	    this.inputCharsLeft.value = smsLenLeft;
    } else {
	    this.inputCharsLeft.value = 0;
	    this.value = msg.value.substring(0, SMS_MAXCHARS);
    }

    // the max len of a single sms is different
    // than the max for a single sms that is part
    // of a multipart message, so we calculate
    // the numbe of smses being sent differently
    // if the number of smses is greater than 1
    // (a multi-part message)
    //
    if (len <= SMS_SINGLE_MAXCHARS) {
        this.inputSmsCount.value= 1;
    } else {
        this.inputSmsCount.value = Math.ceil(len / SMS_SINGLE_MULTIPART_MAXCHARS);
    }
}

// wire up sms input box with its associated counts
//
function wireupSmsCountUpdate(inputMsg, inputCharsLeft, inputSmsCount) {
  inputMsg.inputCharsLeft= inputCharsLeft;
  inputMsg.inputSmsCount= inputSmsCount;
  inputMsg.updateSmsCounts= updateSmsCounts;
}


// END -->
