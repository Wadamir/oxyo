<?php

/**
 * Report catalog folders whose names do not match the file manager naming rules.
 * This script does not rename or modify any files.
 *
 * Usage: php tools/check_catalog_folder_names.php [directory]
 */

$root = isset($argv[1]) ? $argv[1] : dirname(__DIR__) . '/image/catalog';
$root = realpath($root);

if ($root === false || !is_dir($root)) {
    fwrite(STDERR, "Directory not found.\n");
    exit(1);
}

function normalizeFolderName($name)
{
    $cyrillicMap = array(
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

    $name = strtr($name, $cyrillicMap);

    if (class_exists('Transliterator')) {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

        if ($transliterator) {
            $name = $transliterator->transliterate($name);
        }
    } elseif (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);

        if ($converted !== false) {
            $name = $converted;
        }
    }

    $name = preg_replace('/[^A-Za-z0-9_-]+/', '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    $name = trim($name, '_');
    $name = strtolower($name);

    return $name !== '' ? $name : 'folder';
}

$invalidFolders = array();
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    if (!$item->isDir() || $item->isLink()) {
        continue;
    }

    $name = $item->getFilename();
    $normalized = normalizeFolderName($name);

    if ($name !== $normalized) {
        $relativePath = ltrim(substr($item->getPathname(), strlen($root)), DIRECTORY_SEPARATOR);
        $invalidFolders[] = array($relativePath, $name, $normalized);
    }
}

if (!$invalidFolders) {
    echo "All folder names are valid.\n";
    exit(0);
}

echo 'Invalid folders found: ' . count($invalidFolders) . "\n";

foreach ($invalidFolders as $folder) {
    echo $folder[0] . ' | ' . $folder[1] . ' => ' . $folder[2] . "\n";
}
