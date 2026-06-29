<?php

namespace utils\helpers;

use Exception;
use php\io\{FileStream, IOException, ResourceStream};
use php\util\{Flow, SharedMap};

/**
 * __construct ($lang = null)
 * Class SystemData
 * @package utils\helpers
 * @packages helpers
 */
class SystemData
{
    /** @var SharedMap */
    public static $localization;

    /** @var SharedMap */
    public static $userData;


    protected $localizationPath = 'res://.data/lang/';
    protected $localizationExt  = '.lang';
    protected $defaultLanguage  = 'en';

    protected $userDataPath     = './userData.cfg';

    public function __construct($lang = null)
    {
        try {
            $this->initLanguage($lang);
        } catch (Exception $e) {
        }

        try {
            $this->initUserData();
        } catch (Exception $e) {
        }
    }

    /**
     * @param $lang
     * @return void
     * @throws Exception
     */
    private function initLanguage($lang): void
    {
        self::$localization = new SharedMap([]);

        if (!ResourceStream::exists($this->localizationPath . $lang . $this->localizationExt)) {
            if (!ResourceStream::exists($this->localizationPath . $this->defaultLanguage . $this->localizationExt)) {
                // files localization not found
                return;
            }

            $lang = $this->defaultLanguage;
        }


        $path = $this->localizationPath . $lang . $this->localizationExt;

        foreach (json_decode(FileStream::getContents($path), true) as $key => $value) {
            self::$localization->set($key, $value);
        }
    }


    public function __destruct()
    {
        $json = json_encode(Flow::of(self::$userData)->toArray(true), JSON_PRETTY_PRINT);

        try {
            FileStream::putContents($this->userDataPath, $json);
        } catch (IOException $e) {
            echo "Error: " . $e->getMessage() . "\r\n";
        }
    }

    /**
     * @return void
     */
    public function initUserData(): void
    {
        self::$userData = new SharedMap([]);

        try {
            foreach (json_decode(FileStream::getContents($this->userDataPath), true) as $key => $value) {
                self::$userData->set($key, $value);
            }
        } catch (IOException $e) {

        }
    }
}