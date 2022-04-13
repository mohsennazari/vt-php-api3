<?php


namespace Tests\Monaz\VirusTotal\ResponseMocks;


class ResponseAbstract
{
    const TYPE_MALICIOUS = "MALICIOUS";
    const TYPE_HARMLESS = "HARMLESS";
    const TYPE_SUSPICIOUS = "SUSPICIOUS";

    public static function get($type): array
    {
        return [
            "attributes" => [
                "reputation" => 0,
                "last_analysis_stats" => self::generateStatsByType($type)
            ]
        ];
    }

    protected static function generateStatsByType($type)
    {
        $total = 92;
        $stats = [
            "harmless" => 0,
            "malicious" => 0,
            "suspicious" => 0,
            "undetected" => rand(0, 10),
            "timeout" => rand(0, 5)
        ];

        switch ($type) {
            case(self::TYPE_MALICIOUS):
                $stats["malicious"] = rand(50, 60);
                $stats["suspicious"] = rand(10, 20);
                $stats["harmless"] = $total - ($stats["malicious"]+$stats["suspicious"]+$stats["undetected"]+$stats["timeout"]);
                break;
            case(self::TYPE_SUSPICIOUS):
                $stats["malicious"] = rand(10, 20);
                $stats["suspicious"] = rand(50, 60);
                $stats["harmless"] = $total - ($stats["malicious"]+$stats["suspicious"]+$stats["undetected"]+$stats["timeout"]);
                break;
            case(self::TYPE_HARMLESS):
                $stats["suspicious"] = rand(5, 10);
                $stats["harmless"] = rand(50, 60);
                $stats["malicious"] = $total - ($stats["harmless"]+$stats["suspicious"]+$stats["undetected"]+$stats["timeout"]);
                break;
            default:
                break;
        }
        return $stats;
    }
}
