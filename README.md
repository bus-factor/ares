# Ares

Ares is a lightweight standalone validation library.

## Usage

// validation schema
$schema = ['type' => 'string', 'required' => true];
// validator
$validator = new \Ares\Validation\Validator($schema);

// data validation (true, false)
$valid = $validator->validate($data);

// get list of validation errors
$errors = $validator->getErrors();

### Rules

#### type

The ```type``` rule defines the expected/allowed value type. Supported types are:

* boolean
* float
* integer
* string

Examples:

$schema = ['type' => 'string'];

#### required

