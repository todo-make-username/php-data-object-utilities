<a id="readme-top"></a>

<div align="center">

<!-- PROJECT SHIELDS -->
[![PHP][php-shield]][php-url]

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]

# PHP Object Helpers
PHP helpers for hydrating and validating objects from arrays using PHP reflection and attributes.

Project name is still pending, maybe I can think of something better later.

[Report Bug](https://github.com/todo-make-username/php-object-helpers/issues)
·
[Request Feature](https://github.com/todo-make-username/php-object-helpers/issues)

</div>

## Why use this?
This library can be fairly powerful as it automates many repetitive tasks that we all encounter in modern PHP. A lot of major PHP features use associative arrays that may or may not be in the correct data type. ***I'm looking at you $_POST. DB query results are also array centric.***

This was from the days of old when everything was an array. In this era of strict typing and cleaner PHP, we have been moving on from our over-reliance of arrays and migrating to more structured data objects with defined properties.

That is the niche that this project was built for.

#### Common Use Cases:
* Typing and validating form data.
* DB Mappers.
* APIs.
* Basically anything that has an array that would be better off as an object.

Did I mention that this library is fully extendable? You don't need to use any of my pre-made attributes. You can easily add your own hydration/tailor/validation attributes. As long as they extend my base attribute classes, the helpers will automatically pick up on them.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Requirements
* PHP >= 8.2

Yup, that's it. Since this doesn't do anything fancy and mostly relies on built-in PHP features, no need for any external libraries for now.

The dev requirements are just the typical phpunit and code sniffer.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Documentation
This library is fairly simple, it contains three object helpers and one wrapper that incorporates all three. It also contains some pre-made attributes for you to use that take care of some of the more common things. I've also built a demo for you to use and play with. How to run the demo is at the bottom of this readme.

#### Useful Info:
* Attributes are run in order from top to bottom.
* Each helper only looks at an object's public properties are looked at.
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
* For simplicity, conversions are also skipped if the data type of the property is some sort of object. That opens too many cans of worms to deal with. You'll need to make your own custom Hydration attribute if you want to populate object properties.

#### Attributes
These hook into the assignment process and use the incoming value to perform an action. This is so you can accept one value and assign a different one. This has some extreme potential because of that. For example, you can easily set up a custom attribute to take an ID, run a query or use a mapper, populate a different object, then assign that to the property instead of that simple ID.

**Setting Attributes**\
These are special attributes that can be used on properties to alter the behavior of the hydrator.
* `#[HydratorSettings()]` - This is the settings attribute that is used to enable or disable certain aspects of the hydrator for the property.
	* **Optional Parameter:** `hydrate: bool` [default: true] - This enables/disables hydration completely for the property. Conversion will not run if this is disabled for obvious reasons.
	* **Optional Parameter:** `convert: bool` [default: true] - This enables/disables the type conversions. Without this, you will probably get exceptions/errors for mismatched types if strict typing is used.
* `#[ConversionSettings()]` - This attribute is used to set certain settings that are used within conversion process. Please use Hydrator settings to disable conversions for the property if desired.
	* **Optional Parameter:** `strict: bool` [default: true] - This setting is to enable or disable strict conversions. For most types using strict conversion, the value before must loosely match the value after: `'123' == 123` (str to int). When it doesn't match, a `ConversionException` is thrown. When strict conversion is off, it will attempt to convert like it normally does, but doesn't check for loose equivalence afterwards. So `'123abc'` will convert, without errors, to `123`. For bool values, it will use the truthy value if `filter_var` fails to convert.

**Normal Attributes**
* `#[FileUpload]` - Specify if a property was an upload(s) and automatically pull the data from $_FILES.
	* **Optional Parameter:** `formatted_uploads: bool` [default: true] - This will format PHPs' gross looking multi-uploads array into an array for each uploaded file as an element with the format of a single upload.
	* **Property Data Type Restriction:** Array compatible fields only.
	* **Future Feature:** Eventually this will detect if the field is an object instead of an array and then try to hydrate the object with the files data. There will also be an optional parameter which will take a class and make an array of those if it is a multi upload.
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
	* **Property Data Type Restriction:** String only.
		* **Future Feature:** Might open it up for arrays at a later date to match PHP's str_replace method.
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
This helper validates the data in an objects public properties and returns `true` if it is all valid. The validation runs on each parameter that has a validation attribute. All failure messages are kept in an array and can be retrieved with the method `getMessages()`.

There is an optional, but recommended, attribute for you to use to customize the validation failure messages for validation attribute. This is explained below.

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
Make a copy of this project via cli or by downloading. Then run this command in the project root to spin up a dev PHP server for the demo:
```shell
php -S localhost:8000 demo/index.php
```

Then use a web browser on the same computer to visit the following url:\
http://localhost:8000/

<p align="right">(<a href="#readme-top">back to top</a>)</p>

_<h5>Spaces already have a use, tab indentation is better. #teamtabs</h5>_

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/todo-make-username/php-object-helpers.svg
[contributors-url]: https://github.com/todo-make-username/php-object-helpers/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/todo-make-username/php-object-helpers.svg
[forks-url]: https://github.com/todo-make-username/php-object-helpers/network/members
[stars-shield]: https://img.shields.io/github/stars/todo-make-username/php-object-helpers.svg
[stars-url]: https://github.com/todo-make-username/php-object-helpers/stargazers
[issues-shield]: https://img.shields.io/github/issues/todo-make-username/php-object-helpers.svg
[issues-url]: https://github.com/todo-make-username/php-object-helpers/issues
[license-shield]: https://img.shields.io/github/license/todo-make-username/php-object-helpers.svg
[license-url]: https://github.com/todo-make-username/php-object-helpers/blob/main/LICENSE
[php-shield]: https://img.shields.io/badge/php->%3D8.2-blue
[php-url]: https://www.php.net/