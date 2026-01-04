<?php
if (mail("star14kid@gmail.com", "OTP Test", "Your OTP is 123456", "From: ANANT <star14kid@gmail.com>")) {
    echo "MAIL SENT";
} else {
    echo "MAIL FAILED";
}
