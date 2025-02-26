<?php

namespace Fanoosa\Sms;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Fanoosa\Sms\Exceptions\InvalidConfigurationException;
use Fanoosa\Sms\Exceptions\SmsSendingFailedException;

class SmsService
{
    /**
     * @var string|Repository|Application|mixed|object|null
     */
    protected string $panel;
    /**
     * @var string
     */
    protected string $mobile;
    /**
     * @var int
     */
    protected int $pattern;
    /**
     * @var array
     */
    protected array $args = [];

    public function __construct()
    {
        $this->panel = config('sms.defaults.sms_panel', 'meliPayamak');
    }

    /**
     * @param string $panel
     * @return $this
     */
    public function panel(string $panel): static
    {
        $this->panel = $panel;
        return $this;
    }

    /**
     * @param string $mobile
     * @return $this
     */
    public function mobile(string $mobile): static
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @param int $pattern
     * @return $this
     */
    public function pattern(int $pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param array $args
     * @return $this
     */
    public function args(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    protected function validateConfiguration(): void
    {
        if (empty($this->mobile))
        {
            throw new InvalidConfigurationException('Mobile number is required.');
        }

        if (empty($this->pattern))
        {
            throw new InvalidConfigurationException('Pattern is required.');
        }

        if ($this->panel === 'meliPayamak' && empty(config('sms.configuration.meliPayamak.pattern_api_key')))
        {
            throw new InvalidConfigurationException('MeliPayamak API key is missing.');
        }
    }

    /**
     * @return array{bodyId: int, to: string, args: array}
     */
    protected function configuration(): array
    {
        return [
            'bodyId' => $this->pattern,
            'to'     => $this->mobile,
            'args'   => $this->args,
        ];
    }

    /**
     * @return bool
     * @throws InvalidConfigurationException
     * @throws SmsSendingFailedException
     */
    public function send(): bool
    {
        $this->validateConfiguration();

        try
        {
            if ($this->panel == 'meliPayamak')
            {
                $response = Http::asJson()
                    ->acceptJson()
                    ->post(config('sms.configuration.meliPayamak.pattern_api_key'), $this->configuration());

                if ($response->successful())
                {
                    return true;
                }

                throw new SmsSendingFailedException('Failed to send SMS: ' . $response->body());
            }

            throw new InvalidConfigurationException('Unsupported SMS panel: ' . $this->panel);
        }
        catch (RequestException $e)
        {
            Log::error('Request exception: ' . $e->getMessage());
            throw new SmsSendingFailedException('Request failed: ' . $e->getMessage());
        }
        catch (\Exception $e)
        {
            Log::error('Unexpected exception: ' . $e->getMessage());
            throw new SmsSendingFailedException('Unexpected error: ' . $e->getMessage());
        }
    }
}
