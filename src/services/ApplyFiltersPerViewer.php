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
    protected Query $query;

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

        $filters = $this->getFilters();

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

    private function getFilters(): array
    {
        $user = Craft::$app->getUser();
        $username = $user->getIdentity()->username;
//        $groups = $this->getUserGroups($user);

        /** @var Settings $settings */
        $settings = Plugin::getInstance()->getSettings();

        return $this->getUserFilters($username, $settings->viewFiltersPerUser);
    }

    private function getUserFilters(string $username, array $viewFiltersPerUser)
    {
        $filters = array_filter($viewFiltersPerUser, function ($record) use ($username) {
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

            default:
                throw new Exception("Invalid key " . $key);
        }
    }
}