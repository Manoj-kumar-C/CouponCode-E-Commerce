
// Send Phone OTP
document.getElementById("phonepe-send-phone-otp").addEventListener("click", function(event) {
    event.preventDefault();

    let phone_number = jQuery('#_pronamic_gateway_upi_qr_phonepe_phone_number').val();
    if (phone_number == "") {
        alert("Please Enter Registered Phone number");
        return;
    }
    
    document.getElementById("publish").click();
});

// Submit OTP
document.getElementById("phonepe-submit-otp").addEventListener("click", function(event) {
    event.preventDefault();

    let phone = document.getElementById("_pronamic_gateway_upi_qr_phonepe_phone_number").value;
    let otp = document.getElementById("_pronamic_gateway_upi_qr_phonepe_otp").value;

    if (phone == "") {
        alert("Please Enter Registered Phone number");
        return;
    }
    if (otp == "") {
        alert("Please Enter OTP");
        return;
    }

    document.getElementById("publish").click();
});