<?php
namespace yamarket_fusion\Module\models;

class Image extends \yamarket_fusion\Base\Model {

	public function watermark($filename, $watermark, $position = 'bottomright') {
		if (!$this->isFile($filename))
			throw new \Exception(DIR_IMAGE . "{$filename} does not exists!");
		if (!$this->isFile($watermark))
			throw new \Exception(DIR_IMAGE . "{$watermark} does not exists!");
		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$watermark_path_hash = md5($watermark);

		$image_old = $filename;
		$image_new = 'cache/yamarket_fusion/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . "-w-{$position}-{$watermark_path_hash}." . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			$path = '';
			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}
			
			$image = new \Image(DIR_IMAGE . $image_old);
			$image_watermark = new \Image(DIR_IMAGE . $watermark);
			$image->watermark($image_watermark, $position);
			$image->save(DIR_IMAGE . $image_new);
		}

		$image_new = str_replace(' ', '%20', $image_new);

		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}

	/**
	 * Get real img path after opencart image resize
	 */
	public function extractImagePathFromUrl($url) {
		$start = ($this->config->get('config_ssl') ?: $this->config->get('config_url')) . 'image/';
		$substr = substr($url, 0, strlen($start));
		// var_dump($url, $substr == $start, $substr , $start);
		return $substr == $start ? substr(str_replace('%20', ' ', $url), strlen($start)) : $url;
	}

	private function isFile($filename) {
		$is_file = is_file(DIR_IMAGE . $filename);

		if ($is_file)
			return substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) == str_replace('\\', '/', DIR_IMAGE);
		
		return false;
	}
}
