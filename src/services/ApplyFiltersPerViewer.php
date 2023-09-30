<?php

namespace matfish\ActivityLog\services;

use Craft;
use craft\db\Query;
use Exception;
use matfish\ActivityLog\models\Settings;
use matfish\ActivityLog\Plugin;
use craft\models\UserGroup;

class ApplyFiltersPerViewer
{
    protected $query;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @throws Exception
     */
    public function apply(): Query
    {
        $user = Craft::$app->getUser();
        $username = $user->getIdentity()->username;

        if (getenv('ENVIRONMENT') === 'dev') {
            $filters = $this->getFilters($username);
        } else {
            $filters = \Craft::$app->cache->getOrSet("activitylog_view_filters_user_{$username}", function () use ($username) {
                return $this->getFilters($username);
            }, 60 * 60 * 24);
        }

        if (count($filters) > 0) {
            foreach ($filters as $key => $value) {
                $this->applyFilter($key, $value);
            }
        }

        return $this->query;
    }

    /**
     * @param \craft\web\User $user
     * @return array
     * @throws \Throwable
     */
    protected function getUserGroups(\craft\web\User $user): array
    {
        $userIdentity = $user->getIdentity();
        $userGroups = array_map(static function (UserGroup $group) {
            return $group->handle;
        }, $user->getIdentity()->getGroups());

        if ($userIdentity->admin) {
            $userGroups[] = 'admin';
        }

        return $userGroups;
    }

    private function getFilters($username): array
    {
//        $groups = $this->getUserGroups($user);

        /** @var Settings $settings */
        $settings = Plugin::getInstance()->getSettings();

        return $this->getUserFilters($username, $settings->viewFilters);
    }

    private function getUserFilters(string $username, array $viewFilters)
    {
        $filters = array_filter($viewFilters, function ($record) use ($username) {
            return in_array($username, $record['users'], true);
        });

        if (count($filters) === 0) {
            return [];
        }

        return $filters[0]['filters'];
    }

    private function applyFilter($key, $value): void
    {
        switch ($key) {
            case 'isCp':
            case 'isAction':
            case 'isAjax':
                $value = $value ? '1' : '0';
                $this->query->andWhere("[[$key]]='{$value}'");
                break;
            case 'siteId':
                if (is_array($value)) {
                    $sites = implode(',', $value);
                    $this->query->andWhere("[[siteId]] IN ({$sites})");
                } else {
                    $this->query->andWhere("[[siteId]]='{$value}'");
                }
                break;
            case 'actions':
                if (is_array($value) && count($value) > 0) {
                    $actions = array_map(static function ($action) {
                        return "'" . json_encode($action) . "'";
                    }, $value);
                    $actions = implode(',', $actions);
                    $this->query->andWhere("isAction=0 OR [[actionSegments]] IN ($actions)");
                }
                break;
            default:
                throw new Exception("Invalid key " . $key);
        }
    }
}