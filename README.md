# CIUnit for CodeIgniter 2

**Note:** This is not compatible with CodeIgniter 3. The original creator of my-ciunit has a new project for that here: [https://github.com/kenjis/ci-phpunit-test](https://github.com/kenjis/ci-phpunit-test).

## Examples

### Controller

```php
class LoginActionTest extends CIUnit_TestCase
{
    public function setUp(): void
    {
        $this->CI = set_controller('login');
    }

    public function testLogin()
    {
        $_POST['useremail'] = 'kitsunde@example.org';
        $_POST['password'] = '123';
        $this->CI->login_action();
        $out = output();
        $this->assertRedirects($GLOBALS['OUT'], 'employee/index');
    }

    public function testTemplateRendered()
    {
        $this->CI->login_action();
        $views = output_views();
        $this->assertContains('login', $views);
    }
}
```

## Install via composer

Add a new "Repository" to your `composer.json` file:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/carlos-algms/CIUnit"
        }
    ]
}
```

```bash
composer require carlos-algms/ciunit main
```

Copy the example test directory into the root of your project (same folder as `application` and `system`):

```bash
cp -R vendor/carlos-algms/ciunit/tests ./
```

### Data base testing

Create `application/config/testing/database.php` for database testing. The database name must end with `_test`.

## Writing tests

The `tests` directory is an example. You are meant to replace the tests with your own.


## Fix paths

In case you don't use standard paths, you can set them in the files:

`tests/bootstrap.php` and `tests/phpunit.xml`

## Run Tests:

From the root of your project, run:

```bash
./vendor/bin/phpunit --testdox -c ./tests
```

The flag `--testdox` makes PHPUnit to print the test names.
