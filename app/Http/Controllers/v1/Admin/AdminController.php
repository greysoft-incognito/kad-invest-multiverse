<?php

namespace App\Http\Controllers\v1\Admin;

use App\Http\Controllers\Controller;
use App\Services\HttpStatus;
use App\Services\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    protected $data_type = [
        'prefered_notification_channels' => 'array',
        'keep_successful_queue_logs' => 'boolean',
        'strict_mode' => 'boolean',
        'slack_debug' => 'boolean',
        'slack_logger' => 'boolean',
        'verify_email' => 'boolean',
        'verify_phone' => 'boolean',
        'token_lifespan' => 'number',
        'use_queue' => 'boolean',
        'force_https' => 'boolean',
    ];

    public function saveSettings(Request $request)
    {
        $this->authorize('can-do', ['configuration']);
        $this->validate($request, [
            'contact_address' => ['nullable', 'string'],
            'currency' => ['required', 'string'],
            'currency_symbol' => ['nullable', 'string'],
            'default_banner' => [Rule::requiredIf(fn () => ! config('settings.default_banner')), 'mimes:jpg,png'],
            'auth_banner' => [Rule::requiredIf(fn () => ! config('settings.auth_banner')), 'mimes:jpg,png'],
            'frontend_link' => ['nullable', 'string'],
            'prefered_notification_channels' => ['required', 'array'],
            'keep_successful_queue_logs' => ['nullable'],
            'site_name' => ['required', 'string'],
            'slack_debug' => ['nullable', 'boolean'],
            'slack_logger' => ['nullable', 'boolean'],
            'token_lifespan' => ['required', 'numeric'],
            'trx_prefix' => ['required', 'string'],
            'verify_email' => ['nullable', 'boolean'],
            'verify_phone' => ['nullable', 'boolean'],
        ]);

        collect($request->all())->except(['_method'])->map(function ($config, $key) use ($request) {
            if ($request->hasFile($key)) {
                (new Media)->delete('default', pathinfo(config('settings.'.$key), PATHINFO_BASENAME));
                $save_name = (new Media)->save('default', $key, $config);
                $config = (new Media)->image('default', $save_name, asset('media/default.jpg'));
            } elseif (($type = collect($this->data_type))->has($key)) {
                if (! is_array($config) && $type->get($key) === 'array') {
                    $config = valid_json($config, true, explode(',', $config));
                } elseif ($type->get($key) === 'boolean') {
                    $config = boolval($config);
                } elseif ($type->get($key) === 'number') {
                    $config = intval($config);
                }
            }
            Config::write("settings.{$key}", $config);
        });

        $settings = collect(config('settings'))
            ->except(['permissions', 'messages', 'system'])
            ->filter(fn ($v, $k) => stripos($k, 'secret') === false)
            ->mergeRecursive([
            ]);

        return $this->buildResponse([
            'data' => collect($settings)->put('refresh', ['settings' => $settings]),
            'message' => 'Configuration Saved.',
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ]);
    }
}
