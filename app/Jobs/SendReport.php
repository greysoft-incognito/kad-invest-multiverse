<?php

namespace App\Jobs;

use App\Mail\ReportGenerated;
use App\Models\v1\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class SendReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Form $report, $batch = null, $title = null)
    {
        $this->report = $report;
        $this->batch = $batch;
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->report->data_emails->each(function ($email) {
            RateLimiter::attempt(
                'send-report:'.$email.$this->batch,
                5,
                function () use ($email) {
                    Mail::to($email->toString())->send(new ReportGenerated($this->report, $this->batch, $this->title));
                },
                30
            );
        });
    }
}
