# unzipme
PHP ZipArchive with progress, --strip-components and regex matching

#### Example 1:
```php
$uz = new UnzipMe('httpd-2.4.25-win32-VC14.zip');

$uz->on_next = function($file, $count) use ($uz)
{

	?>
	<script>
		parent.update('Unzipping <?= $file; ?>', <?= round($count/$uz->file_count*100); ?>);
	</script>
	<?php
	
};

$uz->unzip($dest", ["strip"=>1, "matches"=>['`/manual/`', '`/include/`', '`.txt$`']]);
```
This would unzip Apache without the leading Apache24/ dir and would exclude the /manual/ and /include/ folders as well as any text files.

#### Example 2:
```php
$uz = new UnzipMe('php-5.6.30-nts-Win32-VC11-x64.zip');

$uz->on_next = function($file, $count) use ($uz)
{

	?>
	<script>
		parent.update('Unzipping <?= $file; ?>', <?= round($count/$uz->file_count*100); ?>);
	</script>
	<?php
	
};

$uz->unzip($dest", ["matches"=>['`.dll$`', '`.exe$`'], "match_excludes"=>false]);
```
Turns matching in the opposite direction. Only dll's and exe's will be extracted.
