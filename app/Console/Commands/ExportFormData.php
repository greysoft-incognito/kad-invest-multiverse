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

        return 0;
    }

    public function export($queue = false)
    {
        $this->info('Exporting form data...');
        $formData = Form::where('data_emails', '!=', null)->get()->map(function ($form) use ($queue) {
            $this->form = $form;
            $form->data()->chunk(1000, function ($items, $sheets) {
                $this->info('Exporting chunk of '.$items->count().' items to sheets '.$sheets.'...');

                $this->pushItem($this->parseItem($items->first())->keys()->toArray());
                $items->each(function ($item) {
                    $this->info('Exporting item '.$item->id.'...');
                    $item = $this->parseItem($item)->toArray();
                    $this->pushItem($item);
                });

                $this->sheets[] = $this->items;
                $this->items = [];
            });

            $this->exportItems($this->sheets, $queue, $this->form);
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

    public function exportItems($items, $queue = false, $form = null)
    {
        if (!is_array($items) || empty($items)) {
            return false;
        }

        if ($queue === true) {
            SendReport::dispatch($this->form);
        } else {
            $this->form->data_emails->each(function ($email) {
                RateLimiter::attempt(
                    'send-report:'.$email,
                    1,
                    function () use ($email) {
                        Mail::to($email->toString())->send(new ReportGenerated($this->form));
                    },
                    5
                );
            });
        }

        return Excel::store(new GenericDataExport($items), 'exports/'.($form->id ?? $this->form->id ?? '').'/data.xlsx', 'protected');
    }
}