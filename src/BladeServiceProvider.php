<?php

namespace Collective\Html;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Supported Blade Directives.
     *
     * @var array
     */
    protected $directives = [
        'entities', 'decode', 'script', 'style', 'image', 'favicon', 'link', 'secureLink', 'linkAsset',
        'linkSecureAsset', 'linkRoute', 'linkAction', 'mailto', 'email', 'ol', 'ul', 'dl', 'meta',
        'tag', 'open', 'model', 'close', 'token', 'label', 'input', 'text', 'password', 'hidden',
        'email', 'tel', 'number', 'date', 'datetime', 'datetimeLocal', 'time', 'url', 'file',
        'textarea', 'select', 'selectRange', 'selectYear', 'selectMonth', 'getSelectOption',
        'checkbox', 'radio', 'reset', 'image', 'color', 'submit', 'button', 'old',
    ];

    /**
     * Register Blade directives.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $namespaces = [
                'Html' => \get_class_methods(HtmlBuilder::class),
                'Form' => \get_class_methods(FormBuilder::class),
            ];

            foreach ($namespaces as $namespace => $methods) {
                foreach ($methods as $method) {
                    if (\in_array($method, $this->directives)) {
                        $snakeMethod = Str::snake($method);
                        $directive = \strtolower($namespace).'_'.$snakeMethod;

                        $bladeCompiler->directive($directive, static function ($expression) use ($namespace, $method) {
                            return "<?php echo $namespace::$method($expression); ?>";
                        });
                    }
                }
            }
        });
    }
}
