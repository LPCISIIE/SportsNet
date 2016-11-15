<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ImageSizeException extends ValidationException
{
	public static $defaultTemplates = [
			self::MODE_DEFAULT => [
					self::STANDARD => 'The picture size must be below 500 Ko.',
			],
	];
}
