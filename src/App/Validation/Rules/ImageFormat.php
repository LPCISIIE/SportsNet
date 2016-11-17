<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ImageFormat extends AbstractRule
{
	
	public function validate($input)
	{
		$image = $_FILES['galerie']['name'];
		$b = false;
		foreach ($image as $name) {
			$pieces = explode(".",$name);
			if(end($pieces) === "jpg" || end($pieces) === "png") {
				$b = true;
			}
		}
		return $b;
	}
}
