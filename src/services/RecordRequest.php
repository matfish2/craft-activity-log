<?php


namespace matfish\ActivityLog\services;


use Craft;
use craft\web\Request;
use craft\web\Response;
use matfish\ActivityLog\Plugin;
use yii\base\Event;
use matfish\ActivityLog\records\ActivityLog as ActivityLogRecord;
use matfish\ActivityLog\models\ActivityLog as ActivityLogModel;

class RecordRequest
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function record(): void
    {
        $req = $this->request;

        $url = $req->getUrl();

        if (str_contains($url, 'cpresources')) {
            return;
        }


        $ps = explode('?', $url);

        if (count($ps) > 1) {
            parse_str($ps[1], $query);
        } else {
            $query = null;
        }

        $payload = $this->getPayload();

        $isAction = $req->isActionRequest;

        $model = new ActivityLogModel([
            'userId' => Craft::$app->user->id ?? null,
            'url' => $ps[0],
            'query' => $query ? json_encode($query, JSON_THROW_ON_ERROR) : null,
            'payload' => $payload,
            'isAjax' => $this->isAjax(),
            'method' => $req->getMethod(),
            'ip' => $req->getUserIP(),
            'userAgent' => $req->getUserAgent(),
            'isCp' => $req->getIsCpRequest(),
            'isAction' => $isAction,
            'actionSegments' => $isAction ? json_encode($req->getActionSegments()) : null,
            'siteId' => Craft::$app->sites->getCurrentSite()->id ?? null
        ]);

        $record = new ActivityLogRecord();

        $record->userId = $model->userId;
        $record->url = $model->url;
        $record->query = $model->query;
        $record->payload = $model->payload;
        $record->ip = $model->ip;
        $record->userAgent = $model->userAgent;
        $record->method = $model->method;
        $record->isAjax = $model->isAjax;
        $record->isCp = $model->isCp;
        $record->siteId = $model->siteId;
        $record->isAction = $model->isAction;
        $record->actionSegments = $model->actionSegments;

        $record->save();

        $start = microtime(true);

        Event::on(Response::class, Response::EVENT_AFTER_SEND, function ($event) use ($record, $start) {
            $record->execTime = round(microtime(true) - $start, 2);
            $record->responseCode = $event->sender->getStatusCode();
            $record->save();
        });
    }

    protected function isAjax(): bool
    {
        return $this->request->isAjax || str_contains($this->request->headers->get('Accept'), 'application/json');
    }

    /**
     * @param object|array $payload
     * @return false|string|null
     * @throws \JsonException
     */
    protected function getPayload(): string|null|false
    {
        $payload = $this->request->getBodyParams();

        if ($payload) {
            if (isset($payload['CRAFT_CSRF_TOKEN'])) {
                $payload['CRAFT_CSRF_TOKEN'] = '[filtered]';
            }

            $passwordKeys = [];

            foreach (array_keys($payload) as $key) {
                if (str_contains($key, 'password')) {
                    $passwordKeys[] = $key;
                }
            }

            foreach ($passwordKeys as $key) {
                $payload[$key] = '[filtered]';
            }
        }

        return $payload ? json_encode($payload, JSON_THROW_ON_ERROR) : null;
    }
}