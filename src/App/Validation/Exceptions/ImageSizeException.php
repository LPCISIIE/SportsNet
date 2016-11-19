<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ImageSizeException extends ValidationException
{
	public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'L\'image ne doit pas peser plus de 500 Ko.',
        ],
	];
}

