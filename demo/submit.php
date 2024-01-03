<?php declare(strict_types=1);

use TodoMakeUsername\ObjectHelpers\ObjectHelper;
use TodoMakeUsername\ObjectHelpersDemo\Util\ObjectFactory;

$Obj            = ObjectFactory::create($_POST['section']);
$NewObj         = null;
$message        = 'Success!';
$serialized_obj = [];

unset($_POST['section']);

if (!is_null($Obj))
{
	try
	{
		$ObjectHelper   = new ObjectHelper($Obj);
		$is_valid       = $ObjectHelper->hydrate($_POST)->tailor()->isValid();
		$NewObj         = $ObjectHelper->getObject();
		$message        = ($is_valid) ? 'Success' : implode(PHP_EOL, $ObjectHelper->getValidatorMessages());
		$serialized_obj = $NewObj->toArray();
	}
	catch(Exception $e)
	{
		$message = $e->getMessage();
	}
}

$response = [
	'message'    => $message,
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);