<?PHP
echo "start\n\n";

//対象dir
$project_log_dir = '[PJC_DIR]';

$find_cmd = '/bin/find ' . $project_log_dir . ' -maxdepth 1 -type d | grep -v "old"';
exec( $find_cmd, $res_find);

if(!count($res_find))
{
	echo "######## no log_dir ########\n";
	exit;
}


foreach ($res_find as $target_dir)
{
	//アーカイブdir作成
	$archive_dir = $target_dir . '/old';
	if(!file_exists($archive_dir) || !is_dir($archive_dir)) exec('sudo /bin/mkdir ' . $archive_dir);

	$target_files = array();
	//対象ファイル 更新日が1日前
	exec('/bin/find ' . $target_dir . ' -maxdepth 1 -mmin +1440 -type f | grep -v "\/\." ' ,$target_files);
	if(!count($target_files))
	{
		echo "######## no archive file ########\n";
		echo "$target_dir\n\n";
		continue;
	}
	//gzip,mv
	foreach ($target_files as $file_path)
	{
		exec('sudo /bin/gzip -9 ' . $file_path);
		exec('sudo /bin/mv ' . $file_path . '.gz ' . $archive_dir);
		echo $file_path . '  =>  ' .  $archive_dir . '/' . $file_path . ".gz\n";
	}
}

echo "\nfin\n";
?>