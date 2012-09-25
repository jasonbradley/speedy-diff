<?php
/**
 * @author Jason Bradley
 *
 *  Creates a diff of two files from large files
 */
class SpeedyDiff
{
    protected $file1;
    protected $file2;
    
    protected $diff_file = '';
    protected $has_difference = false;
    
    public function __construct($file1, $file2))
    {
        if (!is_file($file1) || !is_file($file2))
        {
            throw new Exception("Provide two files.");
        }

        
        $this->file1 = $file1;
        $this->file2 = $file2;
        
        $this->runDiff();
    }
    
    protected function createDiffFile($tmp_file)
    {
        if (!is_file($tmp_file))
        {
            throw new Exception("No diff file provided.");
        }
        
        $this->diff_file = tempnam('/tmp', 'speeddifffinal');

        $tmp_diff_handle = fopen($tmp_file, 'r');
        $diff_file_handle = fopen($this->diff_file, 'a');

        while (($line = fgets($tmp_diff_handle)) !== false) 
        {
            $first_char = substr($line, 0, 1);
            
            if ($first_char == '>')
            {
                fwrite($diff_file_handle, substr($line, 2));
            }
        }
        
        fclose($tmp_diff_handle);
        fclose($diff_file_handle);
        
        //remove the tmp file
        unlink($tmp_file);
        
        if (!is_file($this->diff_file))
        {
            throw new Exception("There was a problem writing the new diff file.");
        }
        else if (file_get_contents($this->diff_file) == '')
        {
            throw new Exception("Nothing was written to the new diff file.");
        }
    }
    
    protected function runDiff()
    {
        $tmp_file = tempnam('/tmp', 'speeddiff');
        $cmd = "diff {$this->file1} {$this->file2} --speed-large-files -b > $tmp_file";
        exec($cmd, $output, $return_var);
        
        unset($output);
        
        if ($return_var > 1)
        {
            unlink($tmp_file);
            throw new Exception("There was an error when calling $cmd");
        }
        else if (!is_file($tmp_file))
        {
            throw new Exception("Unable to write to tmp file, $tmp_file");
        }
        else if (file_get_contents($tmp_file) == '')
        {
            return;
        }
        
        $this->has_difference = true;
        
        $this->createDiffFile($tmp_file);
    }
    
    /**
     * Get the file created from the diff
     * 
     * @return String|Boolean
     */
    public function getDiffFile()
    {
        return (is_file($this->diff_file)) ? $this->diff_file : false;
    }
    
    public function getHasDifference()
    {
        return $this->has_difference;
    }
}
