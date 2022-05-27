<?php

namespace App\Classes;

use Illuminate\Foundation\Application;

class Localization
{
    /**
     * Cached copy of the configured supported locales
     *
     * @var array
     */
    protected array $configuredSupportedLocales = [];

    /**
     * Our instance of the Laravel app
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Make a new Locale instance
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Retrieve the currently set locale
     *
     * @return string
     */
    public function current(): string
    {
        return $this->app->getLocale();
    }

    /**
     * Retrieve the configured fallback locale
     *
     * @return string
     */
    public function fallback(): string
    {
        return $this->app->make('config')['app.fallback_locale'];
    }

    /**
     * Set the current locale
     *
     * @param string $locale
     */
    public function set(string $locale)
    {
        $this->app->setLocale($locale);
    }

    /**
     * Retrieve the current locale's directionality
     *
     * @return string
     */
    public function dir(): string
    {
        return $this->getConfiguredSupportedLocales()[$this->current()]['dir'];
    }

    /**
     * Retrieve the name of the current locale in the app's
     * default language
     *
     * @param string $locale
     * @return string
     */
    public function nameFor(string $locale): string
    {
        return $this->getConfiguredSupportedLocales()[$locale]['name'];
    }

    /**
     * Retrieve all of our app's supported locale language keys
     *
     * @return array
     */
    public function supported(): array
    {
        return array_keys($this->getConfiguredSupportedLocales());
    }

    /**
     * Determine whether a locale is supported by our app
     * or not
     *
     * @param string $locale
     * @return bool
     */
    public function isSupported(string $locale): bool
    {
        return in_array($locale, $this->supported());
    }

    /**
     * Retrieve our app's supported locale's from configuration
     *
     * @return array
     */
    protected function getConfiguredSupportedLocales(): array
    {
        // cache the array for future calls
        if (empty($this->configuredSupportedLocales)) {
            $this->configuredSupportedLocales = $this->app->make('config')['app.supported_locales'];
        }

        return $this->configuredSupportedLocales;
    }
}
