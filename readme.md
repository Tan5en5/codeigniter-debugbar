## Requirements

- PHP 5.3.2+ (Composer requirement)
- CodeIgniter 3.0.x

## Installation

Create `composer.json` file in your application's root if there is none. Add the following text in the file: 
```json
{
    "require": {
        "tan5en5/codeigniter-debugbar": "dev-master"
    }
}
```
Enable Composer (locate in `./config/config.php`) :
```php
$config['composer_autoload'] = FCPATH.'vendor/autoload.php';
```
In your application, you will first need to load the newly installed package. This is  done easily through the autoloader, but could also be done in your controller with an environment check for maximum optimization. 
```php
$autoload['packages'] = array(APPPATH.'third_party/codeigniter-debugbar');
```
If you want to log messages and exceptions you can also load console library
```php
$autoload['libraries'] = array('console');
```
To use it.
```php
$this->console->exception(new Exception('test exception'));
$this->console->debug('Debug message');
$this->console->info('Info message');
$this->console->warning('Warning message');
$this->console->error('Error message');
```
Then, enable the profiler like normal.
```php
$this->output->enable_profiler(true);
```

To complete the installation, add the following header tags :
```html
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/github.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
```

**Important** : If there is a profiler configuration file in your application config directory delete it or CodeIgniter will not load our configuration file.

## Configuration

Configuration file is located in `./third_party/codeigniter-debugbar/config/profiler.php`.

To configure the profiler, read [CodeIgniter's profiler documentation](http://www.codeigniter.com/userguide3/general/profiling.html).

CodeIgniter Debug Bar adds 3 new sections :

- CodeIgniter infos : Display informations about CodeIgniter (version, environment and locale).
- Messages : Display messages (Console library must be loaded).
- Exceptions : Display exceptions (Console library must be loaded).

You can configure PHP Debug Bar directly into the profiler configuration file, read [PHP Debug Bar documentation](http://phpdebugbar.com/docs/rendering.html#rendering) for more information.

### Advanced AJAX

By default ajax debug data are send through headers but if you are sending a lot of data it may cause problems with your browser. If you set `open_handler_url` in the configuration file, it will use a storage handler and the open handler to load the data after an ajax request.

Here is an example of an `open_handler_url` setting.

```php
$config['open_handler_url'] = get_instance()->config->site_url('debug/open_handler');
```

This code will be in `./controllers/Debug.php`

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use DebugBar\DebugBar;
use DebugBar\OpenHandler;
use DebugBar\Storage\FileStorage;

class Debug extends CI_Controller 
{
    public function open_handler()
    {
        $this->output->enable_profiler(false);
        $this->config->load('profiler', true);
        $path = $this->config->item('cache_path', 'profiler');
		$cache_path = ($path === '') ? APPPATH.'cache/debugbar/' : $path;
        $debugbar = new DebugBar();
        $debugbar->setStorage(new FileStorage($cache_path));
        $openHandler = new OpenHandler($debugbar);
        $data = $openHandler->handle(null, false, false);

        $this->output
            ->set_content_type('application/json')
            ->set_output($data);
    }
}

```

## License

The MIT License (MIT)

Copyright (c) 2014-2015 Anthony Tansens

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
