<?php

namespace App\Console\Commands;

use App\Mail\ReportGenerated;
use App\Models\v1\Form;
use App\Models\v1\GenericFormData;
use App\Services\GenericDataExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
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
    protected $signature = 'export:formdata';

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
        $this->export();

        return 0;
    }

    public function export()
    {
        $this->info('Exporting form data...');
        Form::where('data_emails', '!=', null)->get()->each(function ($form) {
            $this->form = $form;
            GenericFormData::query()->chunk(300, function ($items, $sheets) {
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

            $this->exportItems($this->sheets, $this->form);
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

            return [$label => $value];
        });
    }

    public function pushItem($item)
    {
        $this->items[] = $item;
    }

    public function exportItems($items)
    {
        $this->form->data_emails->each(function ($email) {
            Mail::to($email->toString())->send(new ReportGenerated($this->form));
        });

        return Excel::store(new GenericDataExport($items), 'exports/'.($this->form->id ?? '').'/data.xlsx', 'protected');
    }
}
