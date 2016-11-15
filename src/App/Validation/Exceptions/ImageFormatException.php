<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ImageFormatException extends ValidationException
{
	public static $defaultTemplates = [
			self::MODE_DEFAULT => [
					self::STANDARD => 'You can only use .jpg/.png images, sorry !',
			],
	];
}
