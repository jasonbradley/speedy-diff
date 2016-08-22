<?php

namespace JasonBradley\SpeedyDiff;

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

        //validate input files
        $this->validateFiles();

        //generate diff output
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
     * Parse the diff file and only return the lines that
     * are different
     *
     * @param  string $diffFile Path to diff file
     * @return string Lines that are different between the two input files
     */
    protected function parseDiffFile($diffFile)
    {
        $differences = "";

        $diffFileHandle = fopen($diffFile, 'r');

        while (($line = fgets($diffFileHandle)) !== false) {
            $firstChar = substr($line, 0, 1);

            if ($firstChar == '>') {
                $differences .= substr($line, 2);
            }
        }

        fclose($diffFileHandle);

        return trim($differences);
    }

    /**
     * Write the differences to a file
     *
     * @param string $diffFile File that contains the diff output
     *
     * @throws \JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    protected function writeDiffFile($diffFile)
    {
        if (!is_file($diffFile)) {
            throw new DiffException('No diff file provided.');
        }

        $differences = $this->parseDiffFile($diffFile);

        $this->setDiffFile($differences);
    }

    /**
     * Run the diff command and generate the diff file from the output.
     *
     * @throws \JasonBradley\SpeedyDiff\Exceptions\DiffException
     */
    protected function runDiff()
    {
        //Generate the difference between the two files and write it to a temp file
        $tmpFile = tempnam('/tmp', 'speedydiff');
        $cmd = "diff {$this->file1} {$this->file2} --speed-large-files -b > $tmpFile";
        exec($cmd, $output, $returnVar);

        //validate the command ran successfully
        if ($returnVar > 1) {
            unlink($tmpFile);
            throw new DiffException("There was an error when calling $cmd");
        } elseif (!is_file($tmpFile)) {
            throw new DiffException("Unable to write to tmp file, $tmpFile");
        } elseif (file_get_contents($tmpFile) == '') { //nothing to diff
            return;
        }

        $this->setHasDifference(true);

        //create the diff file
        $this->writeDiffFile($tmpFile);
    }

    /**
     * Write to the diff file
     * @param string $contents
     */
    protected function setDiffFile($contents)
    {
        $this->diffFile = tempnam('/tmp', 'speedydifffinal');

        file_put_contents($this->diffFile, $contents);

        if (!is_file($this->diffFile)) {
            throw new DiffException('There was a problem writing the new diff file.');
        } elseif (file_get_contents($this->diffFile) == '') {
            throw new DiffException('Nothing was written to the new diff file.');
        }
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
     * Set hasDifference
     * @param boolean $hasDifference Is there a difference between the input files?
     */
    protected function setHasDifference($hasDifference)
    {
        $this->hasDifference = $hasDifference;
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
