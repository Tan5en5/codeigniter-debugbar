## Requirements

- PHP 5.3.2+ (Composer requirement)
- CodeIgniter 3.x

## Installation

Create `composer.json` file in your application's root if there is none. Add the following text in the file: 
```json
{
    "require": {
        "tan5en5/codeigniter-debugbar": "dev-master"
    }
}
```
In your application, you will first need to load the newly installed package.  This is  done easily through the autoloader, but could also be done in your controller with an environment check for maximum optimization. 
```php
$autoload['packages'] = array(APPPATH.'third_party/codeigniter-debugbar');
```
If you want to log messages and exceptions you can also put in the autoloader the console library
```php
$autoload['libraries'] = array('console');
```
To use it.
```php
$this->console->exception(new Exception('test exception'));
$this->console->debug('Debug message');
$this->console->info('Info message');
$this->console->warning('Warning message');
$this->console->notice('Error message');
```
Then, just enable the profiler like normal.
```php
$this->output->enable_profiler(true);
```
**NOTE** : Make sure to put `$head_src` variable (it adds javascript and css files) in your html template head tag.

**NOTE 2** : If there is a profiler configuration file in your application config directory delete it or it won't work.

**NOTE 3** : Make sur the `$config['base_url']` directory is readable.

## Configuration

You can configure PHP Debug Bar directly into the profiler configuration file, read [PHP Debug Bar documentation](http://phpdebugbar.com/docs/rendering.html#rendering) for more information.

## License

[MIT licence](http://opensource.org/licenses/MIT)