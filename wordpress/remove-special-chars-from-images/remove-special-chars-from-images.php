<?php

add_filter('sanitize_file_name', function ($filename) {
	$filename = strtolower(remove_accents($filename));
	$filename = str_replace([' ', '%20', '_'], '-', $filename);
	$filename = preg_replace('/[^a-z0-9.-]/', '', $filename);
	// Keep only the last dot so the extension survives.
	$filename = preg_replace('/\.(?=.*\.)/', '', $filename);
	$filename = trim(preg_replace('/-+/', '-', $filename), '-');
	$filename = str_replace('-.', '.', $filename);

	// A name made only of stripped characters would leave ".jpg", a hidden file.
	return ('' === $filename || str_starts_with($filename, '.')) ? 'file' . $filename : $filename;
});
