<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ImageFormatException extends ValidationException
{
	public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Extension non autorisée. Veuillez envoyer une image au format .jpg ou .png.',
        ],
	];
}

