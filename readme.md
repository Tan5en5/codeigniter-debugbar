## Requirements

- PHP 5.5+ (recommended by CodeIgniter)
- CodeIgniter 3.1.x

## Installation

Create `composer.json` file in your application's root if there is none. Add the following text in the file: 
```json
{
    "require": {
        "tan5en5/codeigniter-debugbar": "dev-master"
    }
}
```
Enable Composer (locate in `application/config/config.php`) :
```php
$config['composer_autoload'] = realpath(APPPATH.'../vendor/autoload.php');
```
Enable Debugbar package (locate in `application/config/autoload.php`) :
```php
$autoload['packages'] = array(APPPATH.'third_party/codeigniter-debugbar');
```
if you want to log messages and exceptions you can also load console library
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
$this->output->enable_profiler(TRUE);
```

To complete the installation, add the following header tags :
```html
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/highlight.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/github.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
```

## Configuration
**Duplicate** configuration file located in `application/third_party/codeigniter-debugbar/config/profiler.php` to `application/config/profiler.php`.

To configure the profiler, read [CodeIgniter profiler documentation](http://www.codeigniter.com/userguide3/general/profiling.html).

CodeIgniter Debug Bar adds 4 new sections :

- CodeIgniter infos : Display informations about CodeIgniter (version, environment and locale).
- Messages : Display messages (Console library must be loaded).
- Exceptions : Display exceptions (Console library must be loaded).
- Included files : Display included or required files.

You can configure PHP Debug Bar directly into the profiler configuration file, read [PHP Debug Bar documentation](http://phpdebugbar.com/docs/rendering.html#rendering) for more information.

## Advanced configuration

### AJAX

By default ajax debug data are send through headers but if you are sending a lot of data it may cause problems with your browser. If you set `open_handler_url` in the configuration file, it will use a storage handler and the open handler to load the data after an ajax request.

Here is an example of an `open_handler_url` setting.

```php
$config['open_handler_url'] = get_instance()->config->site_url('debug/open_handler');
```

This code will be in `application/controllers/Debug.php`

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
        $this->output->enable_profiler(FALSE);
        $this->config->load('profiler', TRUE);
        $path = $this->config->item('cache_path', 'profiler');
        $cache_path = ($path === '') ? APPPATH.'cache/debugbar/' : $path;
        $debugbar = new DebugBar();
        $debugbar->setStorage(new FileStorage($cache_path));
        $openHandler = new OpenHandler($debugbar);
        $data = $openHandler->handle(NULL, FALSE, FALSE);

        $this->output
            ->set_content_type('application/json')
            ->set_output($data);
    }
}

```

### Output

There is two options that can be use to handle custom profiler output.

- `display_assets`: Whether display content's assets (default: TRUE)
- `display_javascript`: Whether display inline script (default: TRUE)

If you set `display_assets` to false you have to handle assets output manually, for this purpose you can use `CI_Profiler::css_assets()` and `CI_Profiler::js_assets()` they behave exactly like `JavascriptRenderer::dumpJsAssets()` and `JavascriptRenderer::dumpJsAssets()` see [PHP Debug Bar documentation](http://phpdebugbar.com/docs/rendering.html#assets) .

If you set `display_javascript` to false you have to handle inline script manually, for this purpose you can use `CI_Profiler::inline_script()` (**IMPORTANT** : It display inline script with &lt;script&gt; tags !).

Here is an exmple of how you can use it:

```php
<?php

    /**
     * This method handle custom profiler output if profiler is enable, except
     * for json output.
     */
    public function _output($output)
    {
        if (stripos($this->output->get_content_type(), 'json') !== false)
        {
            echo $output;
            return;
        }

        if ($this->output->enable_profiler)
        {
            $this->appendAssets()
                ->appendBody('<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>')
                ->appendBody('<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/highlight.min.js"></script>')
                ->appendHeader('<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/github.min.css">')
                ->appendHeader('<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">')
                ->appendInlineScript(trim(str_replace(['<script type="text/javascript">', '</script>'], ['', ''], CI_Profiler::inline_script())));
        }

        echo $output;
    }

    /**
     * This method will write PhpDebugbar assets files if they don't exist in
     * public directory and add them to the output with custom functions.
     */
    protected function appendAssets()
    {
        $files = ['css_assets' => 'public/css/PhpDebugbar.css', 'js_assets' => 'public/js/PhpDebugbar.js'];

        foreach ($files as $function => $filepath)
        {
            if (!file_exists(FCPATH.$filepath))
            {
                forward_static_call_array(array('CI_Profiler', $function), array(FCPATH.$filepath));
            }
        }

        return $this->appendBody('<script type="text/javascript" src="'.base_url($files['js_assets']).'"></script>')
            ->appendHeader('<link rel="stylesheet" href="'.base_url($files['css_assets']).'">');
    }
```

**IMPORTANT** : Functions to handle profiler output can only be use in CodeIgniter controller function `_output()`.

## License

The MIT License (MIT)

Copyright (c) 2014-2017 Anthony Tansens

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
