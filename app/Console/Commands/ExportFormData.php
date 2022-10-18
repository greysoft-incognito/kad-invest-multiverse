<?php

namespace App\Console\Commands;

use App\Jobs\SendReport;
use App\Mail\ReportGenerated;
use App\Models\v1\Form;
use App\Models\v1\GenericFormData;
use App\Services\GenericDataExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Maatwebsite\Excel\Facades\Excel;

class ExportFormData extends Command
{
    protected $items = [];

    protected $sheets = [];

    protected $form;
    protected $batch = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:formdata {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helps prepare and export generic form data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queue = $this->option('queue');

        $this->export($queue);
        $this->export($queue, true);

        return 0;
    }

    public function export($queue = false, $scanned = false)
    {
        $query = Form::query();

        if ($scanned === true) {
            $query->whereHas('data', function($q) {
                $q->whereHas('scans');
            });
            $this->info('Exporting scanned form data...');
        } else {
            $this->info('Exporting form data...');
        }

        $formData = $query->where('data_emails', '!=', null)->get()->map(function ($form) use ($queue, $scanned) {
            $this->form = $form;
            $this->batch++;
            $form->data()->chunk(1000, function ($items, $sheets) use ($form) {
                $this->info('Exporting chunk of '.$items->count().' items to sheets '.$sheets.' of ' . $form->name . '...');

                $this->pushItem($this->parseItem($items->first())->keys()->toArray());
                $items->each(function ($item) {
                    $this->info('Exporting item '.$item->id.' ('.$item->name_attribute.')...');
                    $item = $this->parseItem($item)->toArray();
                    $this->pushItem($item);
                });

                $this->sheets[] = $this->items;
            });

            $title = $scanned ? $form->name . '(Scanned data)' : $form->name;
            $this->exportItems($this->sheets, $queue, $form, $title);
            $this->items = [];
            $this->info('Done!');
        });
    }

    public function parseItem($item)
    {
        return $item->form->fields->mapWithKeys(function ($field) use ($item) {
            $value = $item->data[$field->name] ?? null;
            $label = $field->label ?? $field->name;

            if ($field->options) {
                $value = collect($field->options)->where('value', $value)->first()['label'] ?? $value;
            }

            return [$label => is_array($value) ? implode(', ', $value) : $value];
        });
    }

    public function pushItem($item)
    {
        $this->items[] = $item;
    }

    public function exportItems($items, $queue = false, $form = null, $title = null)
    {
        $form = $form ?? $this->form;

        if (!is_array($items) || empty($items)) {
            return false;
        }

        if ($queue === true) {
            SendReport::dispatch($form, $this->batch);
        } else {
            $form->data_emails->each(function ($email) use ($form) {
                RateLimiter::attempt(
                    'send-report:'.$email,
                    5,
                    function () use ($email, $form) {
                        Mail::to($email->toString())->send(new ReportGenerated($form, $this->batch));
                    },
                );
            });
        }

        return Excel::store(
            new GenericDataExport($items, $form, $title),
            'exports/' . ($form->id ?? $form->id ?? 'form') . '/data-batch' . $this->batch . '.xlsx',
            'protected'
        );
    }
}
