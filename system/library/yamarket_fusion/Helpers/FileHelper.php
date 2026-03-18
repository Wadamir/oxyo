<?php
namespace yamarket_fusion\Helpers;

class FileHelper {

	/**
	 * Removes a directory (and all its content) recursively.
	 *
	 * @param string $dir the directory to be deleted recursively.
	 */
	public static function removeDirectory($dir) {
		if (!is_dir($dir)) {
			return;
		}
		
		$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}

		rmdir($dir);
	}
}