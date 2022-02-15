<?php

namespace Infinity\Http\Controllers\Traits;

trait AlertsMessages
{
    protected array $alerts = [];

    /**
     * Get all alerts.
     *
     * @param bool $group
     *
     * @return array
     */
    protected function getAlerts(bool $group = false): array
    {
        if (isset($this->alerts['alerts'])) {
            $alerts = $this->alerts['alerts'];

            if ($group) {
                $alerts = collect($alerts)->groupBy('type')->toArray();
            }

            return $alerts;
        }

        return [];
    }

    /**
     * Create an alert.
     *
     * @param $message
     * @param $type
     *
     * @return array
     */
    protected function alert($message, $type): array
    {
        $this->alerts['alerts'][] = [
            'type'    => $type,
            'message' => $message,
        ];

        return $this->alerts;
    }

    /**
     * Create a success alert.
     *
     * @param $message
     *
     * @return array
     */
    protected function alertSuccess($message): array
    {
        return $this->alert($message, 'success');
    }

    /**
     * Create an info alert.
     *
     * @param $message
     *
     * @return array
     */
    protected function alertInfo($message): array
    {
        return $this->alert($message, 'info');
    }

    /**
     * Create a warning alert.
     *
     * @param $message
     *
     * @return array
     */
    protected function alertWarning($message): array
    {
        return $this->alert($message, 'warning');
    }

    /**
     * Create an error alert.
     *
     * @param $message
     *
     * @return array
     */
    protected function alertError($message): array
    {
        return $this->alert($message, 'error');
    }

    /**
     * Create an exception alert.
     *
     * @param \Exception $e
     * @param string     $prefixMessage
     *
     * @return array
     */
    protected function alertException(\Exception $e, string $prefixMessage = ''): array
    {
        return $this->alertError("{$prefixMessage} ".__('infinity::generic.exception').": {$e->getMessage()}");
    }
}
