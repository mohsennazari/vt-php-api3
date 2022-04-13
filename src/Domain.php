<?php


namespace Monaz\VirusTotal;


class Domain extends BaseClient
{
    /**
     * Get scan report by its identifier.
     *
     * @see https://developers.virustotal.com/reference/domain-info
     * @param string $domain
     */
    public function getReport(string $domain): array
    {
        return $this->makeGetRequest("domains/$domain");
    }
}
