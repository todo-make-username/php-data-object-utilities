<a id="readme-top"></a>

<div align="center">

<!-- PROJECT SHIELDS -->
[![PHP][php-shield]][php-url]

[![Contributors][contributors-shield]][contributors-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]

# PHP8 - Data Object Utilities
This library bridges the gap between loosely-typed associative arrays and data objects with typed properties to use in modern type-oriented codebases.

[Report Bug](https://github.com/todo-make-username/php-data-object-utilities/issues)
·
[Request Feature](https://github.com/todo-make-username/php-data-object-utilities/issues)

</div>

## Why use this?
If you are always working with associative arrays to pass data around and would like to use properly typed objects instead, this is the library for you.

## Overview
This purpose of this library is to increase reusability and eliminate many of the repetitive tasks and potential bugs that come from working with typed data in PHP. One main pain point for anyone working in PHP is processing and validating associative arrays that come from various sources ($_POST, PDO, json_decode, etc). Then we run into the repetitive of having to revalidate that the data we want exists in the array, and it is the correct type, every time we use that data in a new method (I mean, you don't have to, but it is safer that way). This can be nearly eliminated by passing around pre-processed data objects (like a struct in other languages) instead of arrays. This library is how we bridge those two pieces.

**There are 4 main actions this library was designed to help with:**
1. Hydrate an object's public properties using an associative array of data.
	* Hydration attributes can act as chainable setter methods that can use the incoming data to assign the object's property something different.
	* For example, when the attribute `#[JsonDecode(true)]` is used on a property, it will expect a json string during hydration and then parses it. Then it uses that array in the next hydration attribute or saves it to the property.
		* That can then be chained with a custom attribute to take the array data and hydrate a different data object to be saved to the property. With just those 2 attributes you removed a lot of processing from your main flow.
2. While hydrating an object, the values from the array will be automatically converted to the property's type if it can.
	* This can be turned off if desired.
3. Clean up an object's values using altering attributes.
	* Things like automatically running `trim`, or `str_replace` on a handful of properties only requires you to add the corresponding attribute to the desired properties on the object.
	* These attributes are called `tailor attributes` in this library. Because a tailor 'alters' clothing. (I really just couldn't think of a better name in the moment)
4. Validate an object's properties using validation attributes.
	* For example, you can set up an attribute that checks if the value of a property matches a regex pattern, or that the value must pass an `!empty` check.

#### Common Use Cases:
* Typing and validating form data.
* Simple DB Mappers.
* APIs.
* Basically anything that has an array that would be better off as a typed object.

Did I mention that this library is fully extendable? You don't need to use any of my pre-made attributes. You can easily add your own hydration/tailor/validation attributes. As long as they extend my base attribute classes, the helpers will automatically pick up on them.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Requirements
* PHP >= 8.2

Yup, that's it. Since this doesn't do anything fancy and mostly relies on built-in PHP features, no need for any external libraries for now.

The dev requirements are just the typical phpunit and code sniffer.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Installation
**Quick Note:** It is not currently set up as a composer package. If a feature request is made in Github Issues, not by me, to add it as a composer package, I'll look into setting that up.

To install via composer, you need to have it look at this repo directly by modifying your `composer.json`. You'll need to add the repo information in the `repositories` section with your desired version number, or add the section if it doesn't exit. Then add the "package" to your `require` section. Then lastly run `composer update`.


composer.json
```
"require": {
    ...,
    "todomakeusername/php-data-object-utilities": "*"
},

...

"repositories": [
    {...},
	{
		"url": "https://github.com/todo-make-username/php-data-object-utilities.git",
		"type": "git",
	},
    {...},
]
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Quick Example of Usage:
In this example we will look at $_POST data. When it comes to arrays, the data doesn't follow strict guidelines which causes bugs. This library simplifies the validation and data processing for you. Below, you'll see I create an object which has properties that corresponds with the expected $_POST keys. By moving away from arrays and towards data driven objects, we can create cleaner code which produces fewer bugs.

Here is a quick and dirty example of how it can be used on $_POST data after a form submission to prepare the data.
```PHP
/**
 * We will be pretending this is coming from a form for a product review.
 *
 * This class has properties that match what we are expecting $_POST to contain.
 * The exception being the file upload one, which looks at $_FILES instead.
 */
class ReviewFormData
{
	#[Trim]     // Tailor Attribute
	#[NotEmpty] // Validation Attribute
	public string $name;

	// For this example, this is a checkbox, and therefore must have a default value if it is unchecked.
	public bool $is_public_review = false;

	public int $star_rating;

	#[StrReplace('*cat sitting on spacebar*', '')] // Tailor Attribute
	#[Trim]                                        // Tailor Attribute
	#[UseDefaultOnEmpty]                           // Tailor Attribute
	public ?string $review_text = null;

	// FileUpload is a hydration attribute that pulls the data automatically from the $_FILES array.
	//		the optional param will format the array into a cleaner format for multi-uploads.
	#[FileUpload(formatted_uploads: true)] // Hydration Attribute
	public array $review_image_uploads;
}

...

// This is the data we got from the form:
// is_public_review was a checkbox. For this example it was unchecked, which doesn't come though.
$_POST = [
	'name' => 'Nonya',
	'review_text' => 'I liked this product.       *cat sitting on spacebar*             ',
	'star_rating' => '4',
];

$_FILES = [
	'review_image_uploads' => [
		// This example will say there are 2 files.
	]
];

...

// Now somewhere else in the codebase where the form data is processed.
$FormObject = new ReviewFormData();

// The object's properties were set using the from $_POST and $_FILES. 
// The values were also converted to the proper types. 
$FormObject = (new ObjectHydrator($FormObject))->hydrate($_POST)->getObject();

// StrReplace ran on the review_text property.
// Trim trimmed the designated properties.
// UseDefaultOnEmpty didn't do anything since that field had a value.
$FormObject = (new ObjectTailor($FormObject))->tailor()->getObject();

// Validation is run. Any failure messages can be retrieved with getMessages.
$ObjectValidator  = new ObjectValidator($FormObject);
$is_valid         = $ObjectValidator->isValid();
$response_message = ($is_valid)
		? 'Success'
		: implode(PHP_EOL, $ObjectValidator->getMessages());

...

// The resulting object property values:
$FormObject->name                 => 'Nonya'
$FormObject->is_public_review     => false
$FormObject->star_rating          => 4
$FormObject->review_text          => 'I liked this product.'
$FormObject->review_image_uploads => [ [ `File 1 Data` ], [ `File 2 Data` ] ]
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Documentation
This library is fairly simple, it contains three object helpers and one wrapper that incorporates all three. It also contains some pre-made attributes for you to use that take care of some of the more common things. I've also built a demo for you to use and play with. How to run the demo is at the bottom of this readme.

#### Useful Info:
* Attributes are run in order from top to bottom.
* Each helper only looks at an object's public properties.
	* Yes, I can technically also do the private/protected properties using reflection. It wont happen because that breaks the whole purpose behind private/protected. That said, if there is a use case that would be deemed essential to have that feature, I can look into opening that up. It better be a good reason though.
* For attribute arguments, use named parameters. It makes things easier for everyone. You can do it the old way if you want, but I recommend using named parameters where you can for self documenting code.
* When using this library to handle form submissions, it is highly recommended to have default values for any property that has form data that may not be sent over. Like checkboxes. Otherwise PHP might start yelling at you about accessing uninitialized properties.
* Hydrating properties which can be converted from a string can be hydrated with an object as long as the `__toString()` magic method is set up.
* Fun Fact: I use the hydrator in all the helpers to hydrate the attribute objects. That is why the abstract attribute classes have public properties.
* Sad Fact: This library cannot work with readonly properties as those can only be set from within the object itself and cannot be changed once set.

Now, on to the actual docs...

<p align="right">(<a href="#readme-top">back to top</a>)</p>


### The Hydrator
This helper takes an assoc array of data and hydrates an object's public properties while also converting the data to the proper types (when it can). The property name must match an array key in the incoming array. The attributes are a way to hook into the assigning of the properties, usually to use the incoming value to do something different than assigning it directly.

#### Basic Usage:
```PHP
$HydratedObj = (new ObjectHydrator($Obj))->hydrate($_POST)->getObject();
```

#### Conversions
When hydrating an object, the data that is passed in is not always the type you need. So I will try and convert it for you behind the scenes.

**Conversion Notes:**
* Bools use PHP's `filter_var` to convert common bool strings. When used with a default value, checkbox values in forms becomes very simple to manage.
* Array conversions are only done on empty values. Everything else will fail. If you want to convert a value to an array, please create a hydration attribute.
* For simplicity, conversions are skipped if the data type of the property is some sort of object. That opens too many cans of worms to deal with. You'll need to make your own custom Hydration attribute if you want to populate object properties.

#### Attributes
These hook into the assignment process and use the incoming value to perform an action. This is so you can accept one value and assign a different one. This has some extreme potential because of that. For example, you can easily set up a custom attribute to take an ID, run a query or use a mapper, populate a different object, then assign that to the property instead of that simple ID.

**Setting Attributes**\
These are special attributes that can be used on properties to alter the behavior of the hydrator.
* `#[HydratorSettings()]` - This is the settings attribute that is used to enable or disable certain aspects of the hydrator for the property.
	* **Optional Parameter:** `hydrate: bool` [default: true] - This enables/disables hydration completely for the property. Type conversions will not run if this is disabled for obvious reasons.
	* **Optional Parameter:** `convert: bool` [default: true] - This enables/disables the type conversions. If set to false, you will probably get exceptions/errors for mismatched types if strict typing is used.
* `#[ConversionSettings()]` - This attribute is used to set certain settings that are used within conversion process. Please use Hydrator settings to disable conversions for the property if desired.
	* **Optional Parameter:** `strict: bool` [default: true] - This setting is to enable or disable strict conversions. For most types using strict conversion, the value before must loosely match the value after: `'123' == 123` (str to int). When it doesn't match, a `ConversionException` is thrown. When strict conversion is off, it will attempt to convert like it normally does, but doesn't check for loose equivalence afterwards. So `'123abc'` will convert, without errors, to `123`. For bool values, it will use the truthy value if `filter_var` fails to convert.

**Normal Attributes**
* `#[FileUpload]` - Specify if a property was an upload(s) and automatically pull the data from $_FILES.
	* **Optional Parameter:** `formatted_uploads: bool` [default: true] - This will format PHP's gross looking multi-uploads array into an array for each uploaded file as an element with the format of a single upload.\
	<code>[ [file1 data], [file2 data] ]</code>
	* **Property Data Type Restriction:** Array compatible fields only.
	* **Special Note:** This will remain an array exclusive because everyone has a different file data class which are all initialized differently. If you want to use your own file data class, ignore this attribute and make a custom hydration attribute which has the logic to set up your desired object.
* `#[JsonDecode]` - Exactly what PHP's `json_decode` does. Takes a JSON string and tries to convert it to an array. The optional constructor arguments match PHP's method as well.
	* **Optional Parameter:** `associative: bool|null` [default: null] - Determines if the value should be parsed as an associative array or not.
	* **Optional Parameter:** `depth: int` [default: 512] - Specified recursion depth.
	* **Optional Parameter:** `flags: int` [default: 0] - Bit mask of JSON decode options.
	* **Property Data Type Restriction:** Array compatible fields only.
* `#[Required]` - This will throw an exception if the property doesn't have a matching key in the incoming array. Basically is just an `isset`.

#### Attribute Properties
These are set when the specific Attribute class is initialized. They can be used in your own attributes if your attribute extends the `AbstractHydratorAttribute` class.

* `public ReflectionProperty $Property;` - The ReflectionProperty object to look up information about the property.
* `public bool $is_set = false;` - This is true if a key matching the property's name is in the incoming array.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


### The Tailor
This helper tailors (aka alters. Ya know, like a tailor does) the data in an objects public properties.

#### Basic Usage:
```PHP
$TailoredObj = (new ObjectTailor($Obj))->tailor()->getObject();
```

#### Attributes
When placed on an object's property, they will alter the value currently in it.

* `#[StrReplace(search: string|array, replace: string|array)]` - This behaves exactly like PHP's `str_replace`.
	* **Property Data Type Restriction:** String and Array only. Those are the types that work in PHP's `str_replace`.
* `#[Trim]` - That's what we all want this library for, now you got it. With the Trim attribute, any data in that property is trimmed.
	* **Property Data Type Restriction:** String only.
	* **Optional Parameter:** `characters: string` - The characters is the same param that is passed to PHP's `trim` function.
* `#[UseDefaultOnEmpty]` - Basically exactly what it says. When the current assigned value passes an `empty` check, reflection looks at the property's default value, and then uses that instead.
	* Pro Tip: Combine with `#[Trim]` to clean up blank form fields with a single space in them.

#### Attribute Properties
These are set when the specific Attribute class is initialized. They can be used in your own attributes if your attribute extends the `AbstractTailorAttribute` class.

* `public ReflectionProperty $Property;` - The ReflectionProperty object to look up information about the property.
* `public bool $is_initialized = false;` - Basically what it says. Tells you if the property has been initialized with a value or not.
	* **IMPORTANT:** This will ALWAYS be true for non-typed properties, even with no default value. Blame PHP's `ReflectionProperty`, not me.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


### The Validator
This helper validates the data in an objects public properties and returns `true` if it is all valid. The validation runs on each parameter that has one or more validation attributes. All failure messages are kept in an array and can be retrieved with the method `getMessages()`.

There is an optional, but recommended, attribute for you to use to customize the validation failure messages for validation attribute. This is explained below in the **Custom Failure Messages** section.

#### Basic Usage:
```PHP
$error_messages = [];
$Validator      = new ObjectValidator($Obj);

if (!$Validator->isValid())
{
	$error_messages = $Validator->getMessages();
}
```

#### Attributes
* `#[NotEmpty]` - The value must pass an `!empty` check.
	* Pro Tip: Combine with `#[Trim]` for validating form fields.
* `#[RegexMatch(pattern: string)]` - Whether or not you can remember how to write regex is a different issue.

#### Attribute Properties
These are set when the specific Attribute class is initialized. They can be used in your own attributes if your attribute extends the `AbstractValidatorAttribute` class.

* `public ReflectionProperty $Property;` - The ReflectionProperty object to look up information about the property.
* `public bool $is_initialized = false;` - Tells you if the property has been initialized with a value or not.

#### Custom Failure Messages
This is not so much a validation attribute as it is a validation helper attribute. This is so that error messages coming from the validation will be more useful for everyone. Here is an example of how it is used on a property.

```PHP
#[Trim]
#[NotEmpty]
#[ValidatorMessage(NotEmpty::class, 'The email field is required!')]
public string $email;
```

When the validation helper analyzes this object, and the value in $email is empty, that error will be added to the messages array and the object will ultimately fail the validation check.

Side note: As you can see, I'm not using named properties for this since it is fairly simple. First param is the validation attribute the message is for, the second param is the failure message. The attribute declaration for these when using named params can get lengthy, especially for longer messages, so I omitted them in all my examples and demo. Feel free to use named params though, it won't break anything to use them, except maybe your linter.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


### The ObjectHelper Wrapper:
This simply wraps all three into a single helper. Everything is processed the same.

#### Basic Usage:
```PHP
$ObjectHelper = new ObjectHelper($Obj);
$is_valid     = $ObjectHelper->hydrate($_POST)->tailor()->isValid();
$NewObj       = $ObjectHelper->getObject();
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## How to run the demo
Make a copy of this project via cli or by downloading. Run `composer install` in the project root, then run the following command (also in the project root) to spin up a dev PHP server for the demo:
```shell
php -S localhost:8000 demo/index.php
```

Then use a web browser on the same computer to visit the following url:\
http://localhost:8000/

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Contributing
**100% Code Coverage ALWAYS!**

I gladly welcome feedback and suggestions via Github Issues. I thrive on constructive feedback.

Bugs of course are submitted via Github Issues as well.

#### Adding New Attributes
When it comes to adding/requesting new attributes into this library, basically just ask yourself: `Would this be useful for everyone? Or just myself?`.\
For attributes that are for specific frameworks, they will not be added in here. I recommend creating an attribute library and importing those into your project along with this project.

#### Code Styling Basics
* Curly braces `{ }` start on new lines. It is not my go to, but it is cleaner to look at.
* Run `composer beautify` before staging your commits.
* Classes are PascalCase.
* Methods are camelCase.
* Variables that hold objects use PascalCase, otherwise they use snake_case.
* Use strict typing as much as possible.

#### Unit Testing Requirements
* 100% Coverage is a hard requirement.
* All tests should run like you are actually using the library.
* Method mocking should be a last resort.
* You need a really good reason to use `@codeCoverageIgnore` or similar flags.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

_<h5>Spaces already have a use, tab indentation is better. #teamtabs</h5>_

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/todo-make-username/php-data-object-utilities.svg
[contributors-url]: https://github.com/todo-make-username/php-data-object-utilities/graphs/contributors
[stars-shield]: https://img.shields.io/github/stars/todo-make-username/php-data-object-utilities.svg
[stars-url]: https://github.com/todo-make-username/php-data-object-utilities/stargazers
[issues-shield]: https://img.shields.io/github/issues/todo-make-username/php-data-object-utilities.svg
[issues-url]: https://github.com/todo-make-username/php-data-object-utilities/issues
[license-shield]: https://img.shields.io/github/license/todo-make-username/php-data-object-utilities.svg
[license-url]: https://github.com/todo-make-username/php-data-object-utilities/blob/main/LICENSE
[php-shield]: https://img.shields.io/badge/php->%3D8.2-blue
[php-url]: https://www.php.net/
