<?php


namespace Monaz\VirusTotal;


use Monaz\VirusTotal\Exceptions\FileNotFoundException;

class File extends BaseClient
{
    /**
     * @param integer MAX_UPLOAD_SIZE
     */
    protected const MAX_UPLOAD_SIZE = 32*1024*1024;

    /**
     * Scan a file from disk by its Absolute path.
     *
     * @see https://developers.virustotal.com/reference/files-scan
     * @param string $file   absolute file path
     * @throws FileNotFoundException
     */
    public function scan(string $file): array
    {
        if(!file_exists($file)) {
            throw new FileNotFoundException();
        }

        $uploadUrl = $this->getUploadUrl($file);
        $payload = [
            [
                'name' => 'file',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file)
            ]
        ];
        $result = $this->makePostRequest($uploadUrl, $payload, 'multipart');
        $result["hash"] = explode(':', base64_decode($result["id"]))[0];

        return $result;
    }

    /**
     * Rescan a file by specified by its identifier.
     *
     * @see https://developers.virustotal.com/reference/files-analyse
     * @param string $resource resource id that you retrieve from scan (MD5, SHA-1, SHA-256)
     */
    public function rescan(string $resource): array
    {
        return $this->makePostRequest("files/$resource/analyse");
    }

    /**
     * Get scan report by its identifier.
     *
     * @see https://developers.virustotal.com/reference/files
     * @see https://developers.virustotal.com/reference/file-info
     * @param string $resource resource id that you retrieve from scan (MD5, SHA-1, SHA-256)
     */
    public function getReport(string $resource): array
    {
        return $this->makeGetRequest("files/$resource");
    }

    /**
     * Get url for uploading files.
     * @see https://developers.virustotal.com/reference/files-upload-url
     *
     * @param string $file
     * @return string
     */
    public function getUploadUrl(string $file): string
    {
        $uploadUrl = "files";
        if(filesize($file) > self::MAX_UPLOAD_SIZE) {
            $response = $this->makeGetRequest("files/upload_url");
            $uploadUrl = $response["data"];
        }

        return $uploadUrl;
    }
}
