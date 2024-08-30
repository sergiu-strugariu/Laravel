<?php

namespace App\Services;


interface EmailServiceInterface
{
    function sendEmail(array $attributes, $template);
}