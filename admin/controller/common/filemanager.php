<?php
class ControllerCommonFileManager extends Controller
{
    private $enableFilenameDebugLog = false; // Set to true to enable detailed filename debug logging

    private function logFilenameDebug($stage, $value, $context = array())
    {
        if (!$this->enableFilenameDebugLog) {
            return;
        }

        $string_value = (string)$value;
        $hex_preview = bin2hex(substr($string_value, 0, 64));
        $message = '[' . date('Y-m-d H:i:s') . '] [filemanager][filename][' . $stage . '] value="' . $string_value . '" len=' . strlen($string_value) . ' hex64=' . $hex_preview;

        if (!empty($context)) {
            $message .= ' context=' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        $log_file = defined('DIR_LOGS') ? DIR_LOGS . 'error.log' : '';

        if ($log_file) {
            @file_put_contents($log_file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            error_log($message);
        }
    }

    private function normalizeToUtf8($value)
    {
        $this->logFilenameDebug('normalizeToUtf8.input', $value);

        if (function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) {
            $this->logFilenameDebug('normalizeToUtf8.already_utf8', $value);
            return $value;
        }

        $encodings = array('Windows-1251', 'CP1251', 'KOI8-R', 'ISO-8859-5');
        $best = '';
        $best_score = -1;

        foreach ($encodings as $encoding) {
            $converted = false;

            if (function_exists('iconv')) {
                $converted = @iconv($encoding, 'UTF-8//IGNORE', $value);
            }

            if (($converted === false || $converted === '') && function_exists('mb_convert_encoding')) {
                $converted = @mb_convert_encoding($value, 'UTF-8', $encoding);
            }

            if ($converted === false || $converted === '' || (function_exists('mb_check_encoding') && !mb_check_encoding($converted, 'UTF-8'))) {
                continue;
            }

            // Prefer conversion that preserves most Cyrillic symbols before transliteration.
            $score = 0;
            if (preg_match_all('/[А-Яа-яЁё]/u', $converted, $matches)) {
                $score = count($matches[0]);
            }

            if ($score > $best_score) {
                $best_score = $score;
                $best = $converted;
            }

            $this->logFilenameDebug('normalizeToUtf8.try.' . $encoding, $converted, array('score' => $score));
        }

        if ($best !== '') {
            $this->logFilenameDebug('normalizeToUtf8.best', $best, array('best_score' => $best_score));
            return $best;
        }

        $this->logFilenameDebug('normalizeToUtf8.fallback_original', $value);
        return $value;
    }

    private function safeBasename($value)
    {
        $value = (string)$value;
        $value = str_replace('\\', '/', $value);
        $parts = explode('/', $value);
        $base = end($parts);

        if ($base === false) {
            return '';
        }

        return $base;
    }

    private function splitFilenameAndExtension($filename)
    {
        $filename = (string)$filename;
        $dot_pos = strrpos($filename, '.');

        if ($dot_pos === false || $dot_pos === 0) {
            return array($filename, '');
        }

        $name = substr($filename, 0, $dot_pos);
        $ext = substr($filename, $dot_pos + 1);

        return array($name, $ext);
    }

    private function transliterateToLatin($value)
    {
        $value = $this->normalizeToUtf8($value);

        $cyrillic_map = array(
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya'
        );

        $value = strtr($value, $cyrillic_map);

        if (class_exists('Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');

            if ($transliterator) {
                $value = $transliterator->transliterate($value);
            }
        } elseif (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

            if ($converted !== false) {
                $value = $converted;
            }
        }

        return $value;
    }

    private function buildUniqueFilename($directory, $filename)
    {
        list($name, $ext) = $this->splitFilenameAndExtension($filename);

        if ($name === '') {
            $name = 'file';
        }

        $ext = $ext !== '' ? '.' . $ext : '';

        $name = utf8_strtolower($name);
        $ext = utf8_strtolower($ext);

        $candidate = $name . $ext;
        $i = 1;

        while (file_exists(rtrim($directory, '/') . '/' . $candidate)) {
            $candidate = $name . '-' . $i . $ext;
            $i++;
        }

        return $candidate;
    }

    private function normalizeUploadedFilename($filename, $directory)
    {
        $this->logFilenameDebug('normalizeUploadedFilename.input', $filename, array('directory' => $directory));

        $decoded = $this->safeBasename($filename);
        $this->logFilenameDebug('normalizeUploadedFilename.safe_basename', $decoded);
        $this->logFilenameDebug('normalizeUploadedFilename.basename', $decoded);

        list($name, $ext) = $this->splitFilenameAndExtension($decoded);
        $ext = utf8_strtolower($ext);
        $this->logFilenameDebug('normalizeUploadedFilename.name_ext', $name, array('ext' => $ext));

        $name = $this->normalizeToUtf8($name);
        $this->logFilenameDebug('normalizeUploadedFilename.after_utf8', $name);

        // Decode entities only after the string is normalized to UTF-8.
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->logFilenameDebug('normalizeUploadedFilename.after_entity_decode', $name);

        // Step 1: replace spaces and unreadable/special symbols with underscore.
        $name = preg_replace('/[\x00-\x1F\x7F]+/', '_', $name);
        $name = str_replace(array(' ', "\t", "\r", "\n"), '_', $name);
        $name = preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $name);
        $this->logFilenameDebug('normalizeUploadedFilename.after_step1', $name);

        // Step 2: convert Cyrillic and any non-Latin symbols to a safe ASCII filename.
        $name = $this->transliterateToLatin($name);
        $this->logFilenameDebug('normalizeUploadedFilename.after_translit', $name);

        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        $name = utf8_strtolower($name);
        $this->logFilenameDebug('normalizeUploadedFilename.after_cleanup', $name);

        if ($name === '') {
            $name = 'file';
            $this->logFilenameDebug('normalizeUploadedFilename.empty_fallback', $name);
        }

        $normalized = $name . ($ext !== '' ? '.' . $ext : '');
        $this->logFilenameDebug('normalizeUploadedFilename.normalized', $normalized);

        $unique = $this->buildUniqueFilename($directory, $normalized);
        $this->logFilenameDebug('normalizeUploadedFilename.unique', $unique);

        return $unique;
    }

    private function isImageOptimizationEnabled()
    {
        $value = $this->config->get('oxyo_optimize_images');

        // Keep optimization enabled by default when the setting is absent.
        return $value !== '0' && $value !== 0;
    }

    private function hasBinary($name)
    {
        $result = shell_exec("which $name");
        return !empty($result);
    }

    private function getMaxImageSize()
    {

        $pairs = [
            ['config_image_thumb_width', 'config_image_thumb_height'],
            ['config_image_popup_width', 'config_image_popup_height'],
            ['config_image_product_width', 'config_image_product_height'],
            ['config_image_additional_width', 'config_image_additional_height'],
            ['config_image_category_width', 'config_image_category_height'],
            ['config_image_cart_width', 'config_image_cart_height'],
            ['config_image_location_width', 'config_image_location_height'],
            ['config_image_manufacturer_width', 'config_image_manufacturer_height']
        ];

        $max_w = 0;
        $max_h = 0;

        foreach ($pairs as $pair) {
            $w = (int)$this->config->get($pair[0]);
            $h = (int)$this->config->get($pair[1]);

            if ($w > $max_w) $max_w = $w;
            if ($h > $max_h) $max_h = $h;
        }

        // fallback to config values if none of the image sizes are set
        if ($max_w <= 0) $max_w = IMAGE_MAX_WIDTH;
        if ($max_h <= 0) $max_h = IMAGE_MAX_HEIGHT;

        // maximum limits to prevent memory issues
        $max_w = min($max_w, IMAGE_MAX_WIDTH);
        $max_h = min($max_h, IMAGE_MAX_HEIGHT);

        return [$max_w, $max_h];
    }

    private function pngHasTransparency($file)
    {
        $handle = fopen($file, 'rb');

        if (!$handle) {
            return false;
        }

        $signature = fread($handle, 8);

        if ($signature !== "\x89PNG\r\n\x1a\n") {
            fclose($handle);

            return false;
        }

        $length_data = fread($handle, 4);
        $chunk_type = fread($handle, 4);

        if (strlen($length_data) !== 4 || $chunk_type !== 'IHDR') {
            fclose($handle);

            return false;
        }

        $chunk_length = unpack('N', $length_data)[1];
        $ihdr = fread($handle, $chunk_length);
        fread($handle, 4);

        if (strlen($ihdr) < 10) {
            fclose($handle);

            return false;
        }

        $color_type = ord($ihdr[9]);

        if ($color_type === 4 || $color_type === 6) {
            fclose($handle);

            return true;
        }

        while (!feof($handle)) {
            $length_data = fread($handle, 4);

            if (strlen($length_data) !== 4) {
                break;
            }

            $chunk_length = unpack('N', $length_data)[1];
            $chunk_type = fread($handle, 4);

            if (strlen($chunk_type) !== 4) {
                break;
            }

            if ($chunk_type === 'tRNS') {
                fclose($handle);

                return true;
            }

            if ($chunk_type === 'IDAT') {
                break;
            }

            fseek($handle, $chunk_length + 4, SEEK_CUR);
        }

        fclose($handle);

        return false;
    }

    private function processOriginalImage($file)
    {
        if (!file_exists($file) || !$this->isImageOptimizationEnabled()) return;

        list($max_w, $max_h) = $this->getMaxImageSize();

        $info = getimagesize($file);
        if (!$info) return;

        $width  = $info[0];
        $height = $info[1];
        $type   = $info[2];

        $original_file = $file;

        // === RESIZE ===
        if ($width > $max_w || $height > $max_h) {

            $ratio = min($max_w / $width, $max_h / $height);
            $new_w = (int)($width * $ratio);
            $new_h = (int)($height * $ratio);

            $image = new Image($file);
            $image->resize($new_w, $new_h);
            $image->save($file, 85);
        }

        // === PNG → JPG ===
        if ($type == IMAGETYPE_PNG && !$this->pngHasTransparency($file)) {

            $jpg = preg_replace('/\.png$/i', '.jpg', $file);
            $jpg_directory = dirname($jpg);
            $jpg_filename = basename($jpg);
            $jpg = rtrim($jpg_directory, '/') . '/' . $this->buildUniqueFilename($jpg_directory, $jpg_filename);

            $img = imagecreatefrompng($file);

            if ($img) {
                imagejpeg($img, $jpg, 85);
                imagedestroy($img);

                $file = $jpg;
                $type = IMAGETYPE_JPEG;
            }
        }

        // === JPEG optimize ===
        if ($type == IMAGETYPE_JPEG && $this->hasBinary('jpegoptim')) {
            exec("jpegoptim --strip-all --max=80 " . escapeshellarg($file));
        }

        // // === WEBP ===
        // if ($this->hasBinary('cwebp')) {
        //     $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);
        //     exec("cwebp -q 85 " . escapeshellarg($file) . " -o " . escapeshellarg($webp));
        // }

        // === DELETE ORIGINAL UPLOADED FILE ===
        if ($original_file !== $file && file_exists($original_file)) {
            unlink($original_file);
        }
    }

    public function index()
    {
        $this->load->language('common/filemanager');

        // Find which protocol to use to pass the full image link back
        if ($this->request->server['HTTPS']) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }

        if (isset($this->request->get['filter_name'])) {
            $filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $this->request->get['filter_name']), '/');
        } else {
            $filter_name = '';
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace('*', '', $this->request->get['directory']), '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $directories = array();
        $files = array();

        $data['images'] = array();

        $this->load->model('tool/image');

        if (substr(str_replace('\\', '/', realpath($directory) . '/' . $filter_name), 0, strlen(DIR_IMAGE . 'catalog')) == str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
            // Get directories
            $directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

            if (!$directories) {
                $directories = array();
            }

            // Get files
            // $files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
            $files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,webp,mp4,webm,ogg,JPG,JPEG,PNG,GIF,WEBP,MP4,WEBM,OGG}', GLOB_BRACE); // Video support

            if (!$files) {
                $files = array();
            }
        }

        // Merge directories and files
        $images = array_merge($directories, $files);

        // Get total number of files and directories
        $image_total = count($images);

        // Split the array based on current page number and max number of items per page of 10
        $images = array_splice($images, ($page - 1) * 16, 16);

        foreach ($images as $image) {
            $name = basename($image);

            if (is_dir($image)) {
                $url = '';

                if (isset($this->request->get['target'])) {
                    $url .= '&target=' . $this->request->get['target'];
                }

                if (isset($this->request->get['thumb'])) {
                    $url .= '&thumb=' . $this->request->get['thumb'];
                }

                $data['images'][] = array(
                    'thumb' => '',
                    'name'  => $name,
                    'type'  => 'directory',
                    'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
                    'href'  => $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . '&directory=' . urlencode(utf8_substr($image, utf8_strlen(DIR_IMAGE . 'catalog/'))) . $url, true)
                );
            } elseif (is_file($image)) {
                // Video support
                $extension = utf8_strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $type = 'image';
                if (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                    $type = 'video';
                }

                $data['images'][] = array(
                    'thumb' => $this->model_tool_image->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), 200, 200),
                    'name'  => $name,
                    // 'type'  => 'image',
                    'type'  => $type, // Video support
                    'extension' => $extension, // Video support
                    'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
                    'href'  => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
                );
            }
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['directory'])) {
            $data['directory'] = urlencode($this->request->get['directory']);
        } else {
            $data['directory'] = '';
        }

        if (isset($this->request->get['filter_name'])) {
            $data['filter_name'] = $this->request->get['filter_name'];
        } else {
            $data['filter_name'] = '';
        }

        // Return the target ID for the file manager to set the value
        if (isset($this->request->get['target'])) {
            $data['target'] = $this->request->get['target'];
        } else {
            $data['target'] = '';
        }

        // Return the thumbnail for the file manager to show a thumbnail
        if (isset($this->request->get['thumb'])) {
            $data['thumb'] = $this->request->get['thumb'];
        } else {
            $data['thumb'] = '';
        }

        // Parent
        $url = '';

        if (isset($this->request->get['directory'])) {
            $pos = strrpos($this->request->get['directory'], '/');

            if ($pos) {
                $url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
            }
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $data['parent'] = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

        // Refresh
        $url = '';

        if (isset($this->request->get['directory'])) {
            $url .= '&directory=' . urlencode($this->request->get['directory']);
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $data['refresh'] = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $url = '';

        if (isset($this->request->get['directory'])) {
            $url .= '&directory=' . urlencode(html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['target'])) {
            $url .= '&target=' . $this->request->get['target'];
        }

        if (isset($this->request->get['thumb'])) {
            $url .= '&thumb=' . $this->request->get['thumb'];
        }

        $pagination = new Pagination();
        $pagination->total = $image_total;
        $pagination->page = $page;
        $pagination->limit = 16;
        $pagination->url = $this->url->link('common/filemanager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $this->response->setOutput($this->load->view('common/filemanager', $data));
    }

    public function upload()
    {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog')) != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
            $json['error'] = $this->language->get('error_directory');
        }

        if (!$json) {
            // Check if multiple files are uploaded or just one
            $files = array();

            if (!empty($this->request->files['file']['name']) && is_array($this->request->files['file']['name'])) {
                foreach (array_keys($this->request->files['file']['name']) as $key) {
                    $files[] = array(
                        'name'     => $this->request->files['file']['name'][$key],
                        'type'     => $this->request->files['file']['type'][$key],
                        'tmp_name' => $this->request->files['file']['tmp_name'][$key],
                        'error'    => $this->request->files['file']['error'][$key],
                        'size'     => $this->request->files['file']['size'][$key]
                    );
                }
            }

            foreach ($files as $file) {
                $filename = '';

                if (is_file($file['tmp_name'])) {
                    // Sanitize the filename
                    $filename = $this->safeBasename($file['name']);
                    $this->logFilenameDebug('upload.raw_filename', $file['name']);
                    $this->logFilenameDebug('upload.safe_basename_filename', $filename);
                    $this->logFilenameDebug('upload.basename_filename', $filename);

                    // Validate the filename length
                    if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                        $json['error'] = $this->language->get('error_filename');
                    }

                    // Allowed file extension types
                    $allowed = array(
                        'jpg',
                        'jpeg',
                        'gif',
                        'png',
                        'mp4',
                        'webm',
                        'ogg', // Video support
                        'webp'
                    );

                    if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    // Allowed file mime types
                    $allowed = array(
                        'image/jpeg',
                        'image/pjpeg',
                        'image/png',
                        'image/x-png',
                        'image/gif',
                        'video/mp4',
                        'video/webm',
                        'video/ogg', // Video support
                        'image/webp'
                    );

                    if (!in_array($file['type'], $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    if ($file['size'] > $this->config->get('config_file_max_size')) {
                        $json['error'] = $this->language->get('error_filesize');
                    }

                    // Return any upload error
                    if ($file['error'] != UPLOAD_ERR_OK) {
                        $json['error'] = $this->language->get('error_upload_' . $file['error']);
                    }
                } else {
                    $json['error'] = $this->language->get('error_upload');
                }

                if (!$json) {
                    $filename = $this->normalizeUploadedFilename($filename, $directory);
                    $this->logFilenameDebug('upload.final_filename', $filename);
                    move_uploaded_file($file['tmp_name'], $directory . '/' . $filename);
                    $this->processOriginalImage($directory . '/' . $filename);
                }
            }
        }

        if (!$json) {
            $json['success'] = $this->language->get('text_uploaded');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function folder()
    {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        // Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
        } else {
            $directory = DIR_IMAGE . 'catalog';
        }

        // Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog')) != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
            $json['error'] = $this->language->get('error_directory');
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // Sanitize the folder name
            $folder = basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
                $json['error'] = $this->language->get('error_folder');
            }

            // Check if directory already exists or not
            if (is_dir($directory . '/' . $folder)) {
                $json['error'] = $this->language->get('error_exists');
            }
        }

        if (!isset($json['error'])) {
            mkdir($directory . '/' . $folder, 0777);
            chmod($directory . '/' . $folder, 0777);

            @touch($directory . '/' . $folder . '/' . 'index.html');

            $json['success'] = $this->language->get('text_directory');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete()
    {
        $this->load->language('common/filemanager');

        $json = array();

        // Check user has permission
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['path'])) {
            $paths = $this->request->post['path'];
        } else {
            $paths = array();
        }

        // Loop through each path to run validations
        foreach ($paths as $path) {
            // Check path exists
            if ($path == DIR_IMAGE . 'catalog' || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $path)), 0, strlen(DIR_IMAGE . 'catalog')) != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
                $json['error'] = $this->language->get('error_delete');

                break;
            }
        }

        if (!$json) {
            // Loop through each path
            foreach ($paths as $path) {
                $path = rtrim(DIR_IMAGE . $path, '/');

                // If path is just a file delete it
                if (is_file($path)) {
                    unlink($path);

                    // If path is a directory beging deleting each file and sub folder
                } elseif (is_dir($path)) {
                    $files = array();

                    // Make path into an array
                    $path = array($path);

                    // While the path array is still populated keep looping through
                    while (count($path) != 0) {
                        $next = array_shift($path);

                        foreach (glob($next) as $file) {
                            // If directory add to path array
                            if (is_dir($file)) {
                                $path[] = $file . '/*';
                            }

                            // Add the file to the files to be deleted array
                            $files[] = $file;
                        }
                    }

                    // Reverse sort the file array
                    rsort($files);

                    foreach ($files as $file) {
                        // If file just delete
                        if (is_file($file)) {
                            unlink($file);

                            // If directory use the remove directory function
                        } elseif (is_dir($file)) {
                            rmdir($file);
                        }
                    }
                }
            }

            $json['success'] = $this->language->get('text_delete');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
