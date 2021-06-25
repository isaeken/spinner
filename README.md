# PHP Spinner

> Elegant spinner for interactive CLI apps.
> 
> PHP alternative for https://github.com/sindresorhus/elegant-spinner

![Spinner](./example/example.gif)

---

````php
use IsaEken\Spinner\Spinner;$result = Spinner::run(function () {
    Spinner::setTitle('Calculating...');
    $a = 1;
    $b = 2;
    $c = $a + $b;
    Spinner::setTitle('Waiting...');
    sleep($c);
    return $c;
});

echo "The result is: $result!";
````

---

## Requirements

- PHP ^8.0
- Windows or Unix (Tested on Windows 10)
- PCNTL extension suggested on unix systems.

> Icons work properly in Windows Terminal application.
> You can create theme to remove or change icons.

---

## Installation

You can install using composer.

````shell
composer require isaeken/spinner
````

---

## Examples

````php
use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Spinner;
use IsaEken\Spinner\Themes\ClassicTheme;

// create spinner (you do not needed this because the 'run' command are automatically creates an instance.)
$spinner = new Spinner();
// or
$spinner = Spinner::getInstance();

// create a spinner process
// with theme
$execution_result = Spinner::run(fn () => 'Hello World!', ClassicTheme::class);
// without theme
$execution_result = Spinner::run(function () {
    // get the spinner instance.
    $spinner = Spinner::getInstance();
    
    // set the process title.
    Spinner::setTitle('Hello World!');
    // alternative
    $spinner->setTitle('Hello World!');
    
    // set the process status.
    Spinner::setStatus(Status::Success);
    Spinner::setStatus(Status::Warning);
    Spinner::setStatus(Status::Failed);
    
    // the end of process
    return 'Hello World!';
});

echo $execution_result; // Hello World!
````

---

## Example Theme

````php
use Illuminate\Support\Collection;
use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Interfaces\ThemeInterface;
use IsaEken\Spinner\Themes\DefaultTheme;

class ExampleTheme extends DefaultTheme implements ThemeInterface
{
    /**
     * @inheritDoc
     */
    public static function frames(): Collection
    {
        return collect([
            '⠋',
            '⠙',
            '⠹',
            '⠸',
            '⠼',
            '⠴',
            '⠦',
            '⠧',
            '⠇',
            '⠏',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function icons(): Collection
    {
        return collect([
            Status::Success => '✔️',
            Status::Warning => '⚠️',
            Status::Failed  => '❌',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function messages(): Collection
    {
        return collect([
            Status::Success => 'Process successfully completed.',
            Status::Warning => 'Process completed but the warnings alerted.',
            Status::Failed  => 'Process cannot be completed successfully.',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function colors(): Collection
    {
        return collect([
            Status::Success => "\e[32m",
            Status::Warning => "\e[33m",
            Status::Failed  => "\e[31m",
        ]);
    }
}
````

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
