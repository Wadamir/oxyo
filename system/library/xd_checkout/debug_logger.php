<?php

class XdCheckoutDebugLogger
{
    private $config;
    private $channel;
    private $logger;

    public function __construct($registry, $channel = 'general', $file = 'xd_checkout.log')
    {
        $this->config = (method_exists($registry, 'has') && $registry->has('config')) ? $registry->get('config') : null;
        $this->channel = trim((string)$channel) !== '' ? (string)$channel : 'general';
        $this->logger = new Log($file);
    }

    public function isEnabled()
    {
        if (!$this->config || !method_exists($this->config, 'get')) {
            return false;
        }

        $settings = $this->config->get('xd_checkout');

        if (!is_array($settings)) {
            return false;
        }

        $value = isset($settings['debug']) ? $settings['debug'] : '';

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (int)$value === 1;
        }

        $value = strtolower(trim((string)$value));

        return in_array($value, array('1', 'on', 'true', 'yes'), true);
    }

    public function write($event, $context = array())
    {
        if (!$this->isEnabled()) {
            return;
        }

        $event = trim((string)$event);

        if ($event === '') {
            $event = 'event';
        }

        if (!is_array($context)) {
            $context = array('value' => $context);
        }

        $line = '[xd_checkout][' . $this->channel . '][' . $event . ']';

        if ($context) {
            $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

            if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
                $options |= JSON_INVALID_UTF8_SUBSTITUTE;
            }

            $json = json_encode($context, $options);
            $line .= ' ' . ($json !== false ? $json : 'context_encode_failed');
        }

        $this->logger->write($line);
    }
}
