<?php

/**
 * UnzipMe!
 *
 * ZipArchive Wrapper with progress and --strip-components functionality.
 *
 * https://github.com/davidbuchweitz/unzipme
 */

class UnzipMe
{
	private $_zip;
	private $_tmp;
	public $on_next = null;
	public $on_done = null;
	public $file_count = 0;

	public function __construct($zip_file)
	{
		$this->_zip = new ZipArchive;

		if (!$this->_zip->open($zip_file))
			throw new Exception("Whitney Houston, we have a problem...");

		$this->_tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid().DIRECTORY_SEPARATOR;
	}

	public function unzip($dest, $options)
	{
		$strip          = (isset($options["strip"]))          ? $options["strip"]          : 0;
		$matches        = (isset($options["matches"]))        ? $options["matches"]        : null;
		$match_excludes = (isset($options["match_excludes"])) ? $options["match_excludes"] : true;

		$this->file_count = $this->_zip->numFiles;

	    for ($i = 0; $i < $this->file_count; $i++)
	    {
	    	$full_path = $this->_zip->statIndex($i)['name'];

    		if ($matches)
    		{
	    		if ($match_excludes)
	    		{
					foreach ($matches as $match)
						if (preg_match($match, $full_path)) continue 2;
	    		}
	    		else
	    		{
					$matched = false;

					foreach ($matches as $match)
						if (preg_match($match, $full_path))
							$matched = true;

					if (!$matched) continue;
				}
			}

	    	$parts = explode("/", $full_path);

	    	if (count($parts) <= $strip)
	    		continue;

			$stripped = implode("/", array_slice($parts, $strip));

	  		if ($this->on_next)
	  			call_user_func($this->on_next, $full_path, $i);

			$this->_zip->extractTo($this->_tmp, $this->_zip->statIndex($i)['name']);

			$dir  = $dest.dirname($stripped);
			$file = basename($stripped);

			if (!is_dir($dir))
				mkdir($dir, 0777, true);

			if (strlen($file)!=0)
				@rename($this->_tmp.$this->_zip->statIndex($i)['name'], $dir.DIRECTORY_SEPARATOR.$file);
	    }

	    $this->_clean($this->_tmp);

	    if ($this->on_done)
	    	call_user_func($this->on_done);
	}

	private function _clean($dir)
	{
    	foreach(scandir($dir) as $file)
    	{
        	if ($file==='.' || $file==='..') continue;
        	(is_dir($dir.DIRECTORY_SEPARATOR.$file)) ? $this->_clean($dir.DIRECTORY_SEPARATOR.$file) : unlink($dir.DIRECTORY_SEPARATOR.$file);
    	}
    	rmdir($dir);
    }
}