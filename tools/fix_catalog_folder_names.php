<?php

/**
 * Rename invalid folders under image/catalog and update matching database paths.
 * Default mode is a dry run. Pass --apply to perform the changes.
 *
 * Usage:
 *   php tools/fix_catalog_folder_names.php
 *   php tools/fix_catalog_folder_names.php --apply
 *   php tools/fix_catalog_folder_names.php --apply /path/to/image/catalog
 */

$apply = in_array('--apply', $argv, true);
$arguments = array_values(array_filter(array_slice($argv, 1), function ($argument) {
    return $argument !== '--apply';
}));
$catalogRoot = isset($arguments[0]) ? $arguments[0] : dirname(__DIR__) . '/image/catalog';
$catalogRoot = realpath($catalogRoot);

if ($catalogRoot === false || !is_dir($catalogRoot)) {
    fwrite(STDERR, "Catalog directory not found.\n");
    exit(1);
}
if (basename($catalogRoot) !== 'catalog' || basename(dirname($catalogRoot)) !== 'image') {
    fwrite(STDERR, "The selected directory must be the OpenCart image/catalog directory.\n");
    exit(1);
}

$configFile = dirname(__DIR__) . '/config.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "OpenCart config.php not found.\n");
    exit(1);
}
require_once $configFile;

$logDirectory = dirname(__DIR__) . '/../storage/logs';
if (!is_dir($logDirectory) && !mkdir($logDirectory, 0775, true) && !is_dir($logDirectory)) {
    fwrite(STDERR, "Could not create the log directory.\n");
    exit(1);
}
$logFile = $logDirectory . '/folder_name_repair_' . date('Ymd_His') . '.log';

function writeRepairLog($message)
{
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    echo $message . PHP_EOL;
}

function normalizeFolderName($name)
{
    $map = array(
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
    $name = strtr($name, $map);

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
    $name = strtolower(trim($name, '_'));

    return $name !== '' ? $name : 'folder';
}

function replaceInValue($value, $replacements, &$changed, &$containsObject)
{
    if (is_string($value)) {
        foreach ($replacements as $old => $new) {
            $value = str_replace($old, $new, $value, $count);
            if ($count > 0) {
                $changed = true;
            }
        }
        return $value;
    }

    if (is_array($value)) {
        $result = array();
        foreach ($value as $key => $item) {
            $newKey = replaceInValue($key, $replacements, $changed, $containsObject);
            $result[$newKey] = replaceInValue($item, $replacements, $changed, $containsObject);
        }
        return $result;
    }

    if (is_object($value)) {
        $containsObject = true;
    }
    return $value;
}

function replaceDatabaseValue($value, $replacements, &$skippedSerialized)
{
    $changed = false;
    $containsObject = false;

    // Re-serialize arrays and strings so serialized byte lengths stay correct.
    if (is_string($value) && preg_match('/^(a|s):/', $value)) {
        $decoded = @unserialize($value, array('allowed_classes' => false));
        if (is_array($decoded) || is_string($decoded)) {
            $decoded = replaceInValue($decoded, $replacements, $changed, $containsObject);
            if ($containsObject) {
                $skippedSerialized = true;
                return $value;
            }
            return $changed ? serialize($decoded) : $value;
        }
    }

    return replaceInValue($value, $replacements, $changed, $containsObject);
}

function sqlIdentifier($identifier)
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function sqlString(mysqli $db, $value)
{
    return "'" . $db->real_escape_string($value) . "'";
}

$directories = array();
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($catalogRoot, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($iterator as $item) {
    if ($item->isDir() && !$item->isLink()) {
        $directories[] = array(
            'absolute' => $item->getPathname(),
            'relative' => ltrim(substr($item->getPathname(), strlen($catalogRoot)), DIRECTORY_SEPARATOR),
            'depth' => substr_count($item->getPathname(), DIRECTORY_SEPARATOR)
        );
    }
}
usort($directories, function ($a, $b) {
    return $a['depth'] <=> $b['depth'];
});

$mapping = array();
$reservedTargets = array();
foreach ($directories as $directory) {
    $oldRelative = str_replace(DIRECTORY_SEPARATOR, '/', $directory['relative']);
    $oldParent = str_replace('\\', '/', dirname($oldRelative));
    if ($oldParent === '.') {
        $oldParent = '';
    }
    $newParent = isset($mapping[$oldParent]) ? $mapping[$oldParent] : $oldParent;
    $newName = normalizeFolderName(basename($oldRelative));
    $baseName = $newName;
    $suffix = 1;
    $reservationKey = $newParent . '/' . $newName;
    $targetRelative = ltrim($reservationKey, '/');
    $targetAbsolute = $catalogRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $targetRelative);

    while (isset($reservedTargets[$reservationKey]) || (file_exists($targetAbsolute) && $targetAbsolute !== $directory['absolute'])) {
        $newName = $baseName . '-' . $suffix++;
        $reservationKey = $newParent . '/' . $newName;
        $targetRelative = ltrim($reservationKey, '/');
        $targetAbsolute = $catalogRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $targetRelative);
    }

    $reservedTargets[$reservationKey] = true;
    $newRelative = ltrim($newParent . '/' . $newName, '/');
    $mapping[$oldRelative] = $newRelative;
}

$pathReplacements = array();
foreach ($mapping as $old => $new) {
    if ($old !== $new) {
        $pathReplacements['catalog/' . $old] = 'catalog/' . $new;
    }
}
uksort($pathReplacements, function ($a, $b) {
    return strlen($b) <=> strlen($a);
});

writeRepairLog(($apply ? 'APPLY' : 'DRY RUN') . ' started; root=' . $catalogRoot . '; log=' . $logFile);
foreach ($pathReplacements as $old => $new) {
    writeRepairLog('PLAN path: ' . $old . ' => ' . $new);
}

if (!$apply) {
    writeRepairLog('No changes made. Re-run with --apply to rename folders and update the database.');
    exit(0);
}

if (DB_DRIVER !== 'mysqli') {
    writeRepairLog('ERROR: This script currently supports the mysqli database driver only.');
    exit(1);
}

$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, (int)DB_PORT);
if ($db->connect_error) {
    writeRepairLog('ERROR: Database connection failed: ' . $db->connect_error);
    exit(1);
}
$db->set_charset('utf8mb4');

$renamed = array();
try {
    // Rename deepest folders first so their original parent paths still exist.
    $operations = array();
    foreach ($mapping as $old => $new) {
        if ($old !== $new) {
            $operations[] = array('old' => $old, 'new' => $new);
        }
    }
    usort($operations, function ($a, $b) {
        return substr_count($b['old'], '/') <=> substr_count($a['old'], '/');
    });

    foreach ($operations as $operation) {
        $oldAbsolute = $catalogRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $operation['old']);
        $oldParent = dirname($oldAbsolute);
        $newAbsolute = $oldParent . DIRECTORY_SEPARATOR . basename($operation['new']);
        if (file_exists($newAbsolute)) {
            throw new RuntimeException('Rename target already exists: ' . $newAbsolute);
        }
        if (!rename($oldAbsolute, $newAbsolute)) {
            throw new RuntimeException('Could not rename folder: ' . $oldAbsolute);
        }
        $renamed[] = array('from' => $newAbsolute, 'to' => $oldAbsolute);
        writeRepairLog('RENAMED: ' . $operation['old'] . ' => ' . $operation['new']);
    }

    $db->begin_transaction();
    $columnQuery = "SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = " . sqlString($db, DB_DATABASE) . "
        AND DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','json','enum','set')
        AND EXTRA NOT LIKE '%GENERATED%'";
    $columnsResult = $db->query($columnQuery);
    $columns = array();
    while ($column = $columnsResult->fetch_assoc()) {
        $columns[$column['TABLE_NAME']]['columns'][$column['COLUMN_NAME']] = $column['DATA_TYPE'];
        if ($column['COLUMN_KEY'] === 'PRI') {
            $columns[$column['TABLE_NAME']]['primary'][] = $column['COLUMN_NAME'];
        }
    }

    $updatedRows = 0;
    foreach ($columns as $table => $tableInfo) {
        if (empty($tableInfo['primary'])) {
            foreach ($tableInfo['columns'] as $column => $type) {
                foreach ($pathReplacements as $old => $new) {
                    $select = 'SELECT DISTINCT ' . sqlIdentifier($column) . ' AS `__value` FROM ' . sqlIdentifier($table)
                        . ' WHERE LOCATE(' . sqlString($db, $old) . ', ' . sqlIdentifier($column) . ') > 0';
                    $rows = $db->query($select);
                    while ($row = $rows->fetch_assoc()) {
                        $skippedSerialized = false;
                        $newValue = replaceDatabaseValue($row['__value'], $pathReplacements, $skippedSerialized);
                        if ($skippedSerialized) {
                            writeRepairLog('SKIPPED serialized object value: ' . $table . '.' . $column);
                            continue;
                        }
                        if ($newValue !== $row['__value']) {
                            $update = 'UPDATE ' . sqlIdentifier($table) . ' SET ' . sqlIdentifier($column) . ' = ' . sqlString($db, $newValue)
                                . ' WHERE ' . sqlIdentifier($column) . ' <=> ' . sqlString($db, $row['__value']);
                            $db->query($update);
                            $updatedRows += $db->affected_rows;
                        }
                    }
                }
            }
            continue;
        }

        $primarySelect = implode(', ', array_map('sqlIdentifier', $tableInfo['primary']));
        foreach ($tableInfo['columns'] as $column => $type) {
            foreach ($pathReplacements as $old => $new) {
                $select = 'SELECT ' . $primarySelect . ', ' . sqlIdentifier($column) . ' AS `__value` FROM ' . sqlIdentifier($table)
                    . ' WHERE LOCATE(' . sqlString($db, $old) . ', ' . sqlIdentifier($column) . ') > 0';
                $rows = $db->query($select);
                while ($row = $rows->fetch_assoc()) {
                    $skippedSerialized = false;
                    $newValue = replaceDatabaseValue($row['__value'], $pathReplacements, $skippedSerialized);
                    if ($skippedSerialized) {
                        writeRepairLog('SKIPPED serialized object value: ' . $table . '.' . $column);
                        continue;
                    }
                    if ($newValue === $row['__value']) {
                        continue;
                    }

                    $where = array();
                    foreach ($tableInfo['primary'] as $primary) {
                        $where[] = sqlIdentifier($primary) . ' <=> ' . sqlString($db, $row[$primary]);
                    }
                    $update = 'UPDATE ' . sqlIdentifier($table) . ' SET ' . sqlIdentifier($column) . ' = ' . sqlString($db, $newValue)
                        . ' WHERE ' . implode(' AND ', $where);
                    $db->query($update);
                    $updatedRows += $db->affected_rows;
                }
            }
        }
    }

    $db->commit();
    writeRepairLog('DATABASE updated rows: ' . $updatedRows);
    writeRepairLog('APPLY completed successfully.');
} catch (Throwable $error) {
    $db->rollback();
    writeRepairLog('ERROR: ' . $error->getMessage());
    foreach (array_reverse($renamed) as $operation) {
        if (file_exists($operation['from']) && !file_exists($operation['to'])) {
            @rename($operation['from'], $operation['to']);
        }
    }
    writeRepairLog('Attempted to roll back folder renames. Review the log and database backup.');
    exit(1);
}
