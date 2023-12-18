<?php declare(strict_types=1);

use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrator;
use TodoMakeUsername\ObjectHelpers\Tailor\ObjectTailor;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidator;
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
		$NewObj          = (new ObjectHydrator($Obj))->hydrate($_POST)->getObject();
		$NewObj          = (new ObjectTailor($NewObj))->tailor()->getObject();
		$ObjectValidator = new ObjectValidator($NewObj);
		$message         = ($ObjectValidator->validate()) ? 'Success' : $ObjectValidator->getMessage();
		$serialized_obj  = $NewObj->toArray();
	}
	catch(Exception $e)
	{
		$message = $e->getMessage();
	}
}

$response = [
	'message'    => $message,
	'hydrated'   => !is_null($NewObj),
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);