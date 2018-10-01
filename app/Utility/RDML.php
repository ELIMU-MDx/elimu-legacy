<?php

namespace App\Utility;

use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nathanmac\Utilities\Parser\Facades\Parser;

class RDML
{
    public const TMP_ZIP_EXTRACT_PATH = 'tmp-rdml';

    public const CONTROL_IDS = ['pos', 'ntc', 'neg'];

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $file;

    /**
     * Parsed xml data
     *
     * @var array
     */
    private $data;

    /**
     * Represents a rdml file
     *
     * @param \Symfony\Component\HttpFoundation\File\File
     */
    private function __construct(File $file)
    {

        $this->file = $file;
    }

    public static function make(File $file, $validate = true)
    {
        $rdml = new self($file);
        if (!$validate) {
            return $rdml;
        }

        return $rdml->isValid() ? $rdml : null;
    }

    public function isValid()
    {
        return $this->containsOnlyOneFile() && $this->hasValidContent();
    }

    public function containsOnlyOneFile()
    {
        $zip = new \ZipArchive();
        if ($zip->open($this->file->getRealPath()) !== true) {
            return false;
        }
        $numberOfFiles = $zip->numFiles;

        $zip->close();

        return $numberOfFiles === 1;
    }

    public function hasValidContent()
    {
        return $this->validateKeys() && $this->validateValues();
    }

    public function validateKeys()
    {
        $requiredKeys = [
            'dateMade',
            'dateUpdated',
            'experimenter',
            'dye',
            'sample',
            'target',
            'experiment',
        ];
        $availableKeys = array_keys($this->getData());
        foreach ($requiredKeys as $key) {
            if (!in_array($key, $availableKeys)) {
                return false;
            }
        }

        return true;
    }

    public function getData()
    {
        if (!$this->data) {
            $zip = new \ZipArchive();
            if ($zip->open($this->file->getRealPath()) !== true) {
                return [];
            }
            $zip->extractTo(storage_path('app/' . self::TMP_ZIP_EXTRACT_PATH));
            $zip->close();

            $xmlPath = Storage::files(self::TMP_ZIP_EXTRACT_PATH)[0];
            try {
                $this->data = Parser::xml(Storage::get($xmlPath));
            } catch (\Exception $e) {
                return [];
            } finally {
                Storage::deleteDirectory(self::TMP_ZIP_EXTRACT_PATH);
            }
        }
        return $this->data;
    }

    public function validateValues()
    {
        return self::atLeastOneSampleExists() && self::allControlsExist();
    }

    public function atLeastOneSampleExists()
    {
        return count(
            array_filter(
                $this->getData()['sample'],
                function ($sample) {
                        return !in_array(strtolower($sample['@id']), self::CONTROL_IDS);
                }
            )
        ) > 0;
    }

    public function allControlsExist()
    {
        return count(
            array_filter(
                $this->getData()['sample'],
                function ($sample) {
                        return in_array(strtolower($sample['@id']), self::CONTROL_IDS);
                }
            )
        ) === count(self::CONTROL_IDS);
    }

    public function getSamples()
    {
        return array_filter(
            $this->getData()['sample'],
            function ($sample) {
                return !in_array(strtolower($sample['@id']), self::CONTROL_IDS);
            }
        );
    }
}