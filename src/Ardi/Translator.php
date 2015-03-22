<?php

namespace Ardi;


/**
 * Provides methods to read the translation files
 *
 * @package Ardi
 */
class Translator
{
    private $allStrings;
    private $commonStrings;
    private $viewStrings;
    private $langDir;

    /**
     * @param string $lang Language to load
     * @param string $view View that requires the translation
     * @param string $langDir Path to the folder that contains the translation files
     * @throws \Exception If a translation file for this language does not exist
     */
    public function __construct($lang, $view = 'common', $langDir = 'lang')
    {
        $this->langDir = $langDir;
        $path = "$this->langDir/$lang.ini";
        if (!file_exists($path)) {
            throw new \Exception("No translation file found for language $lang in folder $langDir");
        }
        $this->allStrings = parse_ini_file($path, true);
        $this->commonStrings = $this->allStrings['common'];
        $this->viewStrings = $this->allStrings[$view];
    }

    /**
     * Obtains a translated string identified by a key.
     * If it can't find it within the view-specific section of the file it will fall back to the "common" section.
     *
     * @param string $key The string to retrieve
     * @return null|string The value of the translation or null if not found
     * @throws \Exception If there isn't a translation for the specified key
     */
    public function get($key)
    {
        try {
            return array_key_exists($key, $this->viewStrings) ? $this->viewStrings[$key] : $this->commonStrings[$key];
        } catch(\Exception $e) {
            throw new \Exception("Could not find a translation for $key");
        }
    }
}
