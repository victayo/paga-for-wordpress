<?php

function paga_generate_reference()
{
    return uniqid('PFN-');
}

function paga_get_SHA512($string)
{
    return hash('sha512',  $string);
}

function paga_is_setup()
{
    $secret = get_option('paga_secret');
    $principal = get_option('paga_credential');
    $hash = get_option('paga_hmac');
    $baseUrl = get_option('paga_url');

    if (!$secret || !$principal || !$hash || !$baseUrl) {
        return false;
    }
    return true;
}
