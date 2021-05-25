<?php

namespace Spatie\FlareClient;

use Exception;
use Spatie\FlareClient\Http\Client;
use Spatie\FlareClient\Truncation\ReportTrimmer;

class Api
{
    protected Client $client;

    protected bool $sendReportsImmediately = false;

    protected array $queue = [];

    public function __construct(Client $client)
    {
        $this->client = $client;

        register_shutdown_function([$this, 'sendQueuedReports']);
    }

    public function sendReportsImmediately(): self
    {
        $this->sendReportsImmediately = true;

        return $this;
    }

    public function report(Report $report)
    {
        try {
            $this->sendReportsImmediately()
                ? $this->sendReportToApi($report)
                : $this->addReportToQueue($report);
        } catch (Exception $e) {
            //
        }
    }

    public function sendTestReport(Report $report)
    {
        $this->sendReportToApi($report);
    }

    protected function addReportToQueue(Report $report)
    {
        $this->queue[] = $report;
    }

    public function sendQueuedReports(): void
    {
        try {
            foreach ($this->queue as $report) {
                $this->sendReportToApi($report);
            }
        } catch (Exception $e) {
            //
        } finally {
            $this->queue = [];
        }
    }

    protected function sendReportToApi(Report $report): void
    {
        $payload = $this->truncateReport($report->toArray());

        $this->client->post('reports', $payload);
    }

    protected function truncateReport(array $payload): array
    {
        return (new ReportTrimmer())->trim($payload);
    }
}
