<?php
namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ImageFormat extends AbstractRule
{
	
	public function validate($input)
	{
		$image = $_FILES["epreuve_pic_link"];
		$pieces = explode(".",$image['name']);
		$b = false;
		if(end($pieces) === "jpg" || end($pieces) === "png") {
			$b = true;
		}
		return $b;
	}
}
