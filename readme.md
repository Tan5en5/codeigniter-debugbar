## Installation

Add to your `composer.json` file or create file in your application's root. Add the following text in the new file: 

    {
        "require": {
            "tan5en5/codeigniter-debugbar": "dev-master"
        }
    }

Thanks to the magic of `compwright/composer-installers` the files are transferred to your application's `third_party` folder. In your application, you will first need to load the newly installed package.  This is  done easily through the autoloader, but could also be done in your controller with an environment check for maximum optimization. 

    $autoload['packages'] = array(APPPATH.'third_party/codeigniter-debugbar');

If you want to log messages and exceptions you can also put in the autoloader the console library

    $autoload['libraries'] = array('console');

Then to use it

    $this->console->exception(new Exception('test exception'));
    $this->console->debug('Debug message');
    $this->console->info('Info message');
    $this->console->warning('Warning message');
    $this->console->notice('Error message');

Then, just enable the profiler like normal.

    $this->output->enable_profiler(true);

*NOTE* : Make sure to put **$head_src** variable (it adds javascript and css files) in your html template header.

*NOTE 2* : If there is a profiler configuration file in your application config directory delete it or it won't work.

*NOTE 3* : Make sur the **$config['base_url']** directory is readable.

## Configuration

You can configure PHP Debug Bar directly into the profiler configuration file, read [PHP Debug Bar documentation](http://phpdebugbar.com/docs/rendering.html#rendering) for more information.

## License

[MIT licence](http://opensource.org/licenses/MIT)