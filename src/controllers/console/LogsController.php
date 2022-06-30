<?php

namespace matfish\ActivityLog\controllers\console;

use Carbon\Carbon;
use craft\console\Controller;
use matfish\ActivityLog\records\ActivityLog;
use yii\helpers\Console;

class LogsController extends Controller
{
    public int $days = 30;

    public function options($actionID): array
    {
        return ['days'];
    }

    public function actionPrune()
    {
        $cutoff = Carbon::today()->subDays($this->days ?? 30);

        $this->stdout('Pruning records before ' . $cutoff->format('d-m-Y') . '...' . PHP_EOL, Console::FG_GREEN);

        ActivityLog::deleteAll(['<', 'dateCreated', $cutoff->format('Y-m-d')]);

        $this->stdout('Done!' . PHP_EOL, Console::FG_GREEN);

        return 1;
    }
}