<?php

namespace Citsk\Library;

use Citsk\Exceptions\FileUploadException;

final class Shared
{

    /**
     * @param bool $includeBraces
     *
     * @return string
     */
    public static function createGUID(bool $includeBraces = false): string
    {
        if (function_exists('com_create_guid')) {

            if ($includeBraces === true) {

                return com_create_guid();
            } else {
                return substr(com_create_guid(), 1, 36);
            }
        } else {
            mt_srand((float) microtime() * 10000);

            $charid = md5(uniqid(rand(), true));
            $guid   = substr($charid, 0, 8) . '-' .

            substr($charid, 8, 4) . '-' .
            substr($charid, 12, 4) . '-' .
            substr($charid, 16, 4) . '-' .
            substr($charid, 20, 12);

            if ($includeBraces) {
                $guid = '{' . $guid . '}';
            }

            return $guid;
        }
    }

    /**
     * @param string $stroke
     * @param string $delimiter
     *
     * @return string
     */
    public static function toCamelCase(string $stroke, string $delimiter = '-'): string
    {

        if (!strpos($stroke, $delimiter)) {
            return $stroke;
        }

        $result   = null;
        $tmpArray = explode($delimiter, $stroke);

        for ($i = 0; $i < count($tmpArray); $i++) {
            $result .= ($i == 0) ? $tmpArray[$i] : ucfirst($tmpArray[$i]);
        }

        return $result;
    }

    /**
     * @param string $stroke
     *
     * @return string|null
     */
    public static function toSnakeCase(string $stroke): ?string
    {
        if (!$stroke) {
            return null;
        }

        return mb_strtolower(preg_replace("/((?<=[^$])[A-Z]|(?<!\d|^)\d)/", '_$1', $stroke));
    }

    /**
     * @param mixed string
     * @param mixed string
     *
     * @return array
     */
    public static function getDateRange(string $start = '1 January', string $end = '31 December'): array
    {
        $currentYear = date('y');
        $startRange  = strtotime("$start $currentYear");
        $endRange    = strtotime("$end $currentYear");

        return [
            'start' => $startRange,
            'end'   => $endRange,
        ];
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function getHashString(int $length = 32): string
    {

        $length = ($length < 4) ? 4 : $length;

        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }

    /**
     * @param string $dir
     *
     * @return void
     */
    public static function removeDirectory(string $dir): void
    {

        if (is_dir($dir)) {
            foreach (glob("$dir/*") as $file) {
                unlink($file);
            }

            rmdir($dir);
        }
    }

    /**
     * @param string|null $path
     *
     * @return array
     */
    public static function uploadFiles(?string $path = null): array
    {

        $uploadsDirHashName = $path ?? self::getHashString(10);
        $uploadsDirFullPath = UPLOADS_DIR . "/$uploadsDirHashName";
        $isFileUploaded     = false;
        $fileMeta           = [];

        @mkdir($uploadsDirFullPath, 0777, true);

        foreach ($_FILES as $file) {

            if (!in_array($file['type'], ALLOWED_MIME)) {
                self::removeDirectory($uploadsDirFullPath);
                throw new FileUploadException("[Upload failed] File {$file['name']} is not allowed");
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (!in_array($extension, ALLOWED_EXT)) {
                self::removeDirectory($uploadsDirFullPath);
                throw new FileUploadException("[Upload failed] Extension .$extension is not allowed");
            }

            if ($file['size'] <= 10485760) {
                $tmpName        = $file['tmp_name'];
                $isFileUploaded = move_uploaded_file($tmpName, "$uploadsDirFullPath/{$file['name']}");

                if ($isFileUploaded) {
                    $fileMeta[] = [
                        'sub_dir'   => $uploadsDirHashName,
                        'file_name' => $file['name'],
                        'size'      => $file['size'],
                        'type'      => $file['type'],
                    ];
                }
            } else {
                throw new FileUploadException("[Upload failed] Size of file {$file['name']} limit reached");
            }
        }

        return $fileMeta;
    }

    /**
     * @return string
     */
    public static function getMysqlTimeStamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param string $rawDate
     * @param string $dateFormat
     *
     * @return string
     */
    public static function convertDate(string $rawDate, string $dateFormat = 'Y-m-d H:i:s'): string
    {
        return date($dateFormat, strtotime($rawDate));
    }

    /**
     * @param string|null $date
     * @param string|null $format
     *
     *  YMD / YMDHI
     *
     * @return string|null
     */
    public static function getFormattedDate(?string $date, ?string $format = 'YMDHI'): ?string
    {

        if (!$date) {
            return null;
        }

        $sliceLength = 20;

        switch ($format) {
            case 'YMDHI':
                $sliceLength = 16;
                break;

            case 'YMD':
                $sliceLength = 10;
                break;

            default:
                $sliceLength = 20;
                break;
        }

        return substr($date, 0, $sliceLength);
    }

    /**
     * @param int $param
     *
     * @return string|null
     */
    public static function getCartrigeState(int $param): ?string
    {

        switch ($param) {
            case 1:
                return 'заправлен';

            case 2:
                return 'восстановлен';

            case 3:
                return 'брак';

            case 4:
                return 'возврат';

            case 0:
                return null;
        }
    }

    /**
     * @return string
     */
    public static function getIpAdress(): string
    {
        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public static function getServiceStateClassName(int $value): string
    {
        switch ($value) {
            case 1:
                return 'new-row';

            case 2:
                return 'warning-row';

            case 3:
                return 'success-row';

            case 5:
                return 'inactive-row';

            default:
                return '';
        }
    }

    /**
     * @param int $role
     *
     * @return string|null
     */
    public static function getUserRoleStroke(?string $role): ?string
    {
        switch ($role) {
            case 1:
                return 'администратор';

            case 2:
                return 'пользователь';

            case 3:
                return 'наблюдатель';

            case 4:
                return 'инженер';

            default:
                return null;
        }
    }

    /**
     * @param string $dbTable
     *
     * @return void
     */
    public static function addRefIdField(string $dbTable): void
    {
        $query = "ALTER TABLE $dbTable ADD ref_id INT(10) NOT NULL AFTER app_id";
    }

    /**
     * @param string $file
     *
     * @return void
     */
    public static function forceDownloadFile(string $file): void
    {

        if (file_exists($file)) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);

            exit;
        }

    }
}
