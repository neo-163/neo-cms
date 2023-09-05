<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->data;
        $send = $mail = Mail::raw($input['content'], function ($message) use ($input) {
            $message->subject($input['subject']);
            $message->to($input['email']);
        });

        Log::info('Send email time: ' . date('Y-m-d H:i:s'));
        Log::info('Send email return: ' . json_encode($send) . PHP_EOL);
        
        // echo true;
    }
}
