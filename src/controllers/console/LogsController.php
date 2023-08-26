<?php

namespace matfish\ActivityLog\controllers\console;

use Carbon\Carbon;
use craft\console\Controller;
use matfish\ActivityLog\records\ActivityLog;
use yii\helpers\Console;

class LogsController extends Controller
{
    public int $days = 30;
    public bool $inter = true;

    public function options($actionID): array
    {
        return ['days','interactive'];
    }

    public function actionPrune()
    {
        $days = $this->days;

        $cutoff = Carbon::today()->subDays($days);

        if (!$this->interactive || $this->confirm("Are you sure you want to permanently delete all records before the last {$days} days (" .$cutoff->format('d-m-Y') . ")?")) {
            $this->stdout('Pruning records before ' . $cutoff->format('d-m-Y') . '...' . PHP_EOL, Console::FG_GREEN);

            ActivityLog::deleteAll(['<', 'createdAt', $cutoff->format('Y-m-d')]);

            $this->stdout('Done!' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Aborted' . PHP_EOL, Console::FG_YELLOW);
        }

        return 1;
    }
}