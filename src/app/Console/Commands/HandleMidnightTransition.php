<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HandleMidnightTransition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeAttendances = Attendance::whereNull('clock_out')->where('clock_in', '<', now()->startOfDay())->get();

        foreach ($activeAttendances as $attendance) {
            // 現在の勤務記録を終了
            $attendance->clock_out = now()->startOfDay()->subSecond();  // 23:59:59
            $attendance->save();

            // 翌日の勤務記録を開始
            $newAttendance = new Attendance([
                'user_id' => $attendance->user_id,
                'clock_in' => now()->startOfDay(),  // 00:00:00
                'status' => '0',  // 出勤中と仮定
            ]);
            $newAttendance->save();
        }
    }
}
