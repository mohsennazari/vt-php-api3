<?php


namespace Monaz\VirusTotal;


class Url extends BaseClient
{
    /**
     * Scan a URL.
     *
     * @see https://developers.virustotal.com/reference/scan-url
     * @param string $url   url of the target
     */
    public function scan(string $url): array
    {
        $result = $this->makePostRequest('urls', [
            "url" => $url
        ]);

        $result["hash"] = explode('-', $result["id"])[1];

        return $result;
    }

    /**
     * Get scan report by its identifier.
     *
     * @see https://developers.virustotal.com/reference/url-info
     * @param string $resource resource id that you retrieve from scan (MD5, SHA-1, SHA-256)
     */
    public function getReport(string $resource): array
    {
        return $this->makeGetRequest("urls/$resource");
    }
}
