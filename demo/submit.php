<?php declare(strict_types=1);

use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrator;
use TodoMakeUsername\ObjectHelpersDemo\Util\ObjectFactory;

$Obj            = ObjectFactory::create($_POST['section']);
$Hydrated_Obj   = null;
$message        = '';
$serialized_obj = [];

unset($_POST['section']);

if (!is_null($Obj)) {
	try {
		$Hydrated_Obj = (new ObjectHydrator())->hydrate($Obj)->with($_POST)->getObject();
		$serialized_obj = $Hydrated_Obj->toArray();
	} catch(Exception $e) {
		$message = $e->getMessage();
	}
} 

$response = [
	'message'    => $message,
	'hydrated'   => !is_null($Hydrated_Obj),
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);