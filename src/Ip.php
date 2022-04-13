<?php


namespace Monaz\VirusTotal;


class Ip extends BaseClient
{
    /**
     * Get scan report by its identifier.
     *
     * @see https://developers.virustotal.com/reference/ip-info
     * @param string $ip is a valid IP address in dotted notation
     */
    public function getReport(string $ip): array
    {
        return $this->makeGetRequest("ip_addresses/$ip");
    }
}
