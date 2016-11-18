<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ImageSize extends AbstractRule
{

	public function validate($input)
	{
		$image = $_FILES['galerie']['size'];
		$b = false;
		foreach ($image as $size) {
			if($size <= 2000000) {
				$b = true;
			}
		}
		return $b;
	}
}
