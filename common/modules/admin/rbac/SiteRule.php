<?php
namespace common\modules\admin\rbac;

use Yii;
use yii\rbac\Rule;

class SiteRule extends Rule
{
    public $name = 'isSiteAllow';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $site_id = null;
        if (array_key_exists('site_id', $params)) {
            $site_id = $params['site_id'];
        }

        if (!empty($site_id)) {
            $roleSites = array();
            $authManager = Yii::$app->getAuthManager();
            $roles = $authManager->getRolesByUser(Yii::$app->user->id);
            foreach ($roles as $role) {
                $roleData = json_decode($role->data, true);
                $roleSites = array_merge($roleSites, $roleData["sites"]);
            }
            return in_array($site_id, $roleSites);
        }

        return false;
    }
}