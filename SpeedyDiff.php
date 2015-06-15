<?php

namespace JasonBradley\SpeedyDiff;

require_once './Exceptions/DiffException.php';

use JasonBradley\SpeedyDiff\Exceptions\DiffException;

/**
 * @author Jason Bradley
 *
 *  Quickly creates a diff of two files from large files using
 *  the Linux "diff" command.
 *
 *  Usage:
 *  $speedyDiff = new \JasonBradley\SpeedyDiff\SpeedyDiff($file1, $file2);
 *
 *  echo $speedyDiff->getDiffOutput();
 */
class SpeedyDiff
{
    /** @var string $file1 **/
    protected $file1;

    /** @var string $file2 **/
    protected $file2;

    /** @var string $diffFile **/
    protected $diffFile = '';

    /** @var bool $hasDifference **/
    protected $hasDifference = false;

    /**
     * @param string $file1 First File
     * @param string $file2 Second File
     */
    public function __construct($file1, $file2)
    {
        $this->file1 = $file1;
        $this->file2 = $file2;

        $this->validateFiles();

        $this->runDiff();
    }

    /**
     * Ensure we have two valid files.
     *
     * @throws \JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    protected function validateFiles()
    {
        if (!is_file($this->file1)) {
            throw new DiffException('File #1 is not valid.');
        }

        if (!is_file($this->file2)) {
            throw new DiffException('File #2 is not valid.');
        }
    }

    /**
     * Generate the diff file.
     *
     * @param string $tmpFile File to write diff to
     *
     * @throws \JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    protected function createDiffFile($tmpFile)
    {
        if (!is_file($tmpFile)) {
            throw new DiffException('No diff file provided.');
        }

        $this->diffFile = tempnam('/tmp', 'speeddifffinal');

        $tmpDiffFileHandle = fopen($tmpFile, 'r');
        $diffFileHandle = fopen($this->diffFile, 'a');

        while (($line = fgets($tmpDiffFileHandle)) !== false) {
            $firstChar = substr($line, 0, 1);

            if ($firstChar == '>') {
                fwrite($diffFileHandle, substr($line, 2));
            }
        }

        fclose($tmpDiffFileHandle);
        fclose($diffFileHandle);

        //remove the tmp file
        unlink($tmpFile);

        if (!is_file($this->diffFile)) {
            throw new DiffException('There was a problem writing the new diff file.');
        } elseif (file_get_contents($this->diffFile) == '') {
            throw new DiffException('Nothing was written to the new diff file.');
        }
    }

    /**
     * Run the diff command and generate the diff file from the output.
     *
     * @throws \JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    protected function runDiff()
    {
        $tmpFile = tempnam('/tmp', 'speedydiff');
        $cmd = "diff {$this->file1} {$this->file2} --speed-large-files -b > $tmpFile";
        exec($cmd, $output, $returnVar);

        unset($output);

        if ($returnVar > 1) {
            unlink($tmpFile);
            throw new DiffException("There was an error when calling $cmd");
        } elseif (!is_file($tmpFile)) {
            throw new DiffException("Unable to write to tmp file, $tmpFile");
        } elseif (file_get_contents($tmpFile) == '') {
            return;
        }

        $this->hasDifference = true;

        $this->createDiffFile($tmpFile);
    }

    /**
     * Get the file created from the diff.
     *
     * @return String|Boolean
     */
    public function getDiffFile()
    {
        return (is_file($this->diffFile)) ? $this->diffFile : false;
    }

    /**
     * Get the difference between the two input files1.
     *
     * @return string
     */
    public function getDiffOutput()
    {
        if ($this->getDiffFile() !== false) {
            return file_get_contents($this->getDiffFile());
        }
    }

    /**
     * Returns if there is a difference between the two files.
     *
     * @return bool
     */
    public function getHasDifference()
    {
        return $this->hasDifference;
    }
}
