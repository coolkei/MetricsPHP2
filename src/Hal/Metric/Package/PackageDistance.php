<?php
declare(strict_types=1);
namespace Hal\Metric\Package;

use Hal\Metric\Metrics;
use Hal\Metric\PackageMetric;

class PackageDistance
{
    public function calculate(Metrics $metrics)
    {
        foreach ($metrics->all() as $each) {
            if ($each instanceof PackageMetric &&$each->getAbstraction() !== null && $each->getInstability() !== null) {
                $each->setNormalizedDistance($each->getAbstraction() + $each->getInstability() - 1);
            }
        }
    }
}
