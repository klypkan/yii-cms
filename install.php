<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <?php
    ini_set('max_execution_time', 300);
    ob_start();
    $commonConfigFile = __DIR__ . '/common/config/main.php';
    $site1ConfigFile = __DIR__ . '/site1/config/main.php';
    $result = array("status" => "", "message" => "");
    if ($_POST["step"] == 0) {
        try {
            $result["installed"] = checkInstallation();
            $result["status"] = "success";
        } catch (Exception $ex) {
            $result["status"] = "error";
            $result["message"] = $ex->getMessage();
        }
    } elseif ($_POST["step"] == 1) {
        try {
            $requirementsCheckerResult = checkRequirements($_POST["type_of_database"]);
            $errors = checkIsWritable();
            if ($requirementsCheckerResult["summary"]["errors"] > 0 || count($errors) > 0) {
                $result["status"] = "error";
                $message = "Unfortunately your server configuration does not satisfy the requirements by this application. Please correct the server configuration and check again.";
                foreach ($requirementsCheckerResult["requirements"] as $requirementsItem) {
                    if ($requirementsItem["error"]) {
                        $message .= "<br><br>" . $requirementsItem["name"] . ": " . $requirementsItem["memo"];
                    }
                }
                foreach ($errors as $errorItem) {
                    $message .= "<br><br>" . $errorItem;
                }
                $result["message"] = $message;
            } else {
                $result["status"] = "success";
            }
        } catch (Exception $ex) {
            $result["status"] = "error";
            $message = $ex->getMessage();
            if (!json_encode($message)) {
                $message = "Error code: " . $ex->getCode();
            }
            $result["message"] = $message;
        }
    } elseif ($_POST["step"] == 2) {
        try {
            checkDatabaseConnection($_POST["type_of_database"], $_POST["db_name"], $_POST["db_username"], $_POST["db_password"], $_POST["db_host"]);
            $result["status"] = "success";
        } catch (Exception $ex) {
            $result["status"] = "error";
            $message = $ex->getMessage();
            if (!json_encode($message)) {
                $message = "Database connection error. Error code: " . $ex->getCode();
            }
            $result["message"] = $message;
        }
    } elseif ($_POST["step"] == 3) {
        try {
            install($_POST["type_of_database"], $_POST["db_name"], $_POST["db_username"], $_POST["db_password"], $_POST["db_host"], $_POST["admin_username"], $_POST["admin_password"], $_POST["admin_email"]);
            $result["status"] = "success";
        } catch (Exception $ex) {
            $result["status"] = "error";
            $message = $ex->getMessage();
            if (!json_encode($message)) {
                $message = "Error code: " . $ex->getCode();
            }
            $result["message"] = $message;
        }
    }
    ob_end_clean();
    header("Content-type: application/json");
    echo json_encode($result);
    ?>
<?php else: ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"
              integrity="sha384-PmY9l28YgO4JwMKbTvgaS7XNZJ30MK9FAZjjzXtlqyZCqBY6X6bXIkM++IkyinN+"
              crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"
                integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
        <title>Yii-CMS Installation</title>
        <style>
            #steps {
                padding-top: 15px;
                padding-bottom: 15px;
                font-size: 24px;
            }

            #steps span.active {
                color: #337ab7;
            }

            .btn-spinner {
                display: none;
            }

            .btn[disabled] .btn-spinner {
                display: inline-block;
            }
        </style>
        <script>
            $(function () {
                var containerEl = $("#container"), step = 0;

                runStep({step: step}, null);

                $("#step-1-btn").on("click", function () {
                    runStep({step: step, type_of_database: $("#type-of-database").val()}, $(this));
                });

                $("#step-2-btn").on("click", function () {
                    validateAndRunStep($(this));
                });

                $("#step-3-btn").on("click", function () {
                    validateAndRunStep($(this));
                });

                $("#passport-input-type-btn").on("click", function () {
                    var inputGroup = $(this).parents(".input-group:first");
                    var adminPassword = inputGroup.find("input[name='admin_password']");
                    if (adminPassword.attr("type") == "text") {
                        adminPassword.attr("type", "password");
                        inputGroup.find(".fa-eye-slash").addClass("hidden");
                        inputGroup.find(".fa-eye").removeClass("hidden");
                    }
                    else {
                        adminPassword.attr("type", "text");
                        inputGroup.find(".fa-eye").addClass("hidden");
                        inputGroup.find(".fa-eye-slash").removeClass("hidden");
                    }
                });

                function validateAndRunStep(btn) {
                    var stepEl = $("#step-" + step);
                    var isValid = validateForm(stepEl);
                    if (isValid) {
                        var data = {};
                        for (var i = 1; i <= step; i++) {
                            $.extend(data, getFormData($("#step-" + i)));
                        }
                        data["step"] = step;
                        runStep(data, btn);
                    }
                }

                function validateForm(form) {
                    var isValid = true, el = null;
                    form.find(".form-control").each(function (index) {
                        el = $(this);
                        var rules = el.attr("data-validation-rule").split(" ");
                        for (var i = 0, length = rules.length; i < length; i++) {
                            switch (rules[i]) {
                                case "required":
                                    isValid = isValid & checkRegexp(el, /\S+/);
                                    break;
                                case "email":
                                    isValid = isValid & checkRegexp(el, /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/);
                                    break;
                                case "password":
                                    isValid = isValid & checkRegexp(el, /^\w{6,}$/);
                                    break;
                            }
                        }
                    });
                    return isValid;
                }

                function getFormData(form) {
                    var data = {};
                    form.find(".form-control").each(function (index) {
                        data[$(this).attr("name")] = $(this).val();
                    });
                    return data;
                }

                function checkRegexp(el, regexp) {
                    if (!(regexp.test(el.val()))) {
                        el.parent().addClass("has-error");
                        return false;
                    } else {
                        el.parent().removeClass("has-error");
                        return true;
                    }
                }

                function runStep(data, btn) {
                    if (btn != null) {
                        btn.prop("disabled", true);
                    }
                    var stepEl = $("#step-" + step);
                    var messageEl = stepEl.find(".alert");
                    messageEl.addClass("hidden");
                    $.ajax({
                        url: "install.php",
                        type: "POST",
                        data: data
                    }).done(function (data) {
                        if (data["status"] == "success") {
                            if (step == 0 && data["installed"]) {
                                step = 4;
                            }
                            else {
                                step++;
                            }
                            showStep();
                        }
                        else {
                            messageEl.html(data.message);
                            messageEl.removeClass("hidden");
                        }
                        if (btn != null) {
                            btn.prop("disabled", false);
                        }
                    }).fail(function (jqXHR, textStatus) {
                        messageEl.html(jqXHR.responseText);
                        messageEl.removeClass("hidden");
                        if (step == 0) {
                            stepEl.find(".form-group").addClass("hidden");
                        }
                        if (btn != null) {
                            btn.prop("disabled", false);
                        }
                    });
                }

                function showStep() {
                    var stepEl = $("#step-" + step);
                    containerEl.children(".step-row").addClass("hidden");
                    stepEl.removeClass("hidden");
                    $("#steps").find(".step").each(function (index) {
                        if (index < step) {
                            $(this).addClass("active");
                        }
                    });
                }
            });
        </script>
    </head>
    <body>
    <div id="container" class="container">
        <div class="row">
            <div id="steps" class="col-xs-12">
                <span class="step">Step 1 &#8250; </span><span class="step">Step 2 &#8250; </span><span
                    class="step">Step 3</span>
            </div>
        </div>
        <div id="step-0" class="row step-row">
            <div class="col-xs-12">
                <h1>Installation Checker</h1>

                <div class="alert alert-danger hidden" role="alert"></div>

                <div class="form-group">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                </div>
            </div>
        </div>
        <div id="step-1" class="row step-row hidden">
            <div class="col-xs-12">
                <h1>Application Requirement Checker</h1>

                <div class="alert alert-danger hidden" role="alert"></div>

                <div class="form-group">
                    <label>Type of database</label>
                    <select id="type-of-database" name="type_of_database" class="form-control">
                        <option value="mysql">MySQL, MariaDB</option>
                        <option value="pgsql">PostgreSQL</option>
                    </select>
                </div>
                <button id="step-1-btn" type="button" class="btn btn-primary run-step-btn">Check<span
                        class="btn-spinner"> <i class="fa fa-spinner fa-pulse  fa-fw"></i></span></button>
            </div>
        </div>
        <div id="step-2" class="row step-row hidden">
            <div class="col-xs-12">
                <h1>Database connection</h1>

                <div class="alert alert-danger hidden" role="alert"></div>

                <div class="form-group">
                    <label>Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="" data-validation-rule="required">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="db_username" class="form-control" value=""
                           data-validation-rule="required">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="db_password" class="form-control" value=""
                           data-validation-rule="required">
                </div>
                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="localhost"
                           data-validation-rule="required">
                </div>
                <button id="step-2-btn" type="button" class="btn btn-primary run-step-btn">Submit<span
                        class="btn-spinner"> <i class="fa fa-spinner fa-pulse  fa-fw"></i></span></button>
            </div>
        </div>
        <div id="step-3" class="row step-row hidden">
            <div class="col-xs-12">
                <h1>Installation</h1>

                <div class="alert alert-danger hidden" role="alert"></div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="admin_username" class="form-control" data-validation-rule="required">
                </div>
                <div class="form-group">
                    <label>Password (must be at least 6 characters)</label>

                    <div class="input-group">
                        <input type="text" name="admin_password" class="form-control"
                               data-validation-rule="password">
                            <span class="input-group-btn input-group-text">
                                <button id="passport-input-type-btn" class="btn btn-default" type="button"><i
                                        class="fa fa-eye hidden" aria-hidden="true"></i><i class="fa fa-eye-slash"
                                                                                           aria-hidden="true"></i>
                                </button>
                            </span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="admin_email" class="form-control" data-validation-rule="required email">
                </div>
                <button id="step-3-btn" type="button" class="btn btn-primary run-step-btn">Install<span
                        class="btn-spinner"> <i class="fa fa-spinner fa-pulse  fa-fw"></i></span></button>
            </div>
        </div>
        <div id="step-4" class="row step-row hidden">
            <div class="col-xs-12">
                <h1>Congratulations!</h1>

                <div class="alert alert-success" role="alert">You have successfully created your Yii-CMS site.</div>

                <p>
                    <a class="btn btn-primary" href="/admin/" role="button">Open admin dashboard</a>
                </p>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php
function checkInstallation()
{
    $content = file_get_contents($GLOBALS["commonConfigFile"]);
    $pos = strpos($content, "#dsn#");
    if ($pos === false) {
        return true;
    }
    return false;
}

function checkRequirements($typeOfDatabase)
{
    require_once __DIR__ . "/vendor/yiisoft/yii2/requirements/YiiRequirementChecker.php";
    $requirementsChecker = new YiiRequirementChecker();
    $requirements = array(
        array(
            'name' => 'PDO extension',
            'mandatory' => true,
            'condition' => extension_loaded('pdo'),
            'by' => 'All DB-related classes',
        ),
        array(
            'name' => 'OpenSSL extension',
            'mandatory' => true,
            'condition' => extension_loaded('openssl'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for  a secret key.',
        ),
    );
    if ($typeOfDatabase == "pgsql") {
        $requirements[] = array(
            'name' => 'PDO PostgreSQL extension',
            'mandatory' => true,
            'condition' => extension_loaded('pdo_pgsql'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for PostgreSQL database.',
        );
    } else {
        $requirements[] = array(
            'name' => 'PDO MySQL extension',
            'mandatory' => true,
            'condition' => extension_loaded('pdo_mysql'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for MySQL database.',
        );
    }
    return $requirementsChecker->checkYii()->check($requirements)->getResult();
}

function checkIsWritable()
{
    $errors = array();
    if (!is_writable($GLOBALS["commonConfigFile"])) {
        $errors[] = 'The file ' . $GLOBALS["commonConfigFile"] . ' is not writable.';
    }
    if (!is_writable($GLOBALS["site1ConfigFile"])) {
        $errors[] = 'The file ' . $GLOBALS["site1ConfigFile"] . ' is not writable.';
    }
    return $errors;
}

function checkDatabaseConnection($typeOfDatabase, $dbName, $username, $password, $host)
{
    require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
    $connection = new \yii\db\Connection([
        'dsn' => formatDSN($typeOfDatabase, $dbName, $host),
        'username' => $username,
        'password' => $password,
    ]);
    $connection->open();
    $connection->close();
}

function install($typeOfDatabase, $dbName, $username, $password, $host, $admin_username, $admin_password, $admin_email)
{
    setDatabaseConnection($typeOfDatabase, $dbName, $username, $password, $host, $GLOBALS["commonConfigFile"]);
    setCookieValidationKey($GLOBALS["site1ConfigFile"]);
    runMigrate();
    setDemoData($admin_username, $admin_password, $admin_email);
}

function setDatabaseConnection($typeOfDatabase, $dbName, $username, $password, $host, $file)
{
    $content = file_get_contents($file);
    $content = str_replace("#dsn#", formatDSN($typeOfDatabase, $dbName, $host), $content);
    $content = str_replace("#username#", $username, $content);
    $content = str_replace("#password#", $password, $content);
    file_put_contents($file, $content);
}

function setCookieValidationKey($file)
{
    $length = 32;
    $bytes = openssl_random_pseudo_bytes($length);
    $key = strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
    $content = file_get_contents($file);
    $content = str_replace("#cookieValidationKey#", $key, $content);
    file_put_contents($file, $content);
}

function runMigrate()
{
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
    require_once __DIR__ . '/common/config/bootstrap.php';
    require_once __DIR__ . '/console/config/bootstrap.php';

    $config = yii\helpers\ArrayHelper::merge(
        require $GLOBALS["commonConfigFile"],
        require __DIR__ . '/console/config/main.php'
    );

    $application = new yii\console\Application($config);
    $exitCode = $application->runAction("migrate", array("interactive" => false));
}


function setDemoData($admin_username, $admin_password, $admin_email)
{
    require_once __DIR__ . '/site1/config/bootstrap.php';

    $config = yii\helpers\ArrayHelper::merge(
        require $GLOBALS["commonConfigFile"],
        require $GLOBALS["site1ConfigFile"]
    );

    $userCount = common\models\User::find()->count();
    if ($userCount > 0) {
        return 0;
    }

    $authManager = Yii::$app->getAuthManager();

    $user = new common\models\User();
    $user->username = $admin_username;
    $user->email = $admin_email;
    $user->setPassword($admin_password);
    $user->generateAuthKey();
    $user->save();

    $site = new common\models\Site();
    $site->name = "Site1";
    $site->url = $_SERVER["HTTP_HOST"];
    $site->language = $config['params']['defaultLanguage'];
    $site->path = "site1";
    $site->save();

    $siteRule = new common\modules\admin\rbac\SiteRule();
    $authManager->add($siteRule);

    $role = $authManager->createRole("admin");
    $role->data = json_encode(array("sites" => [$site->id]));
    $authManager->add($role);
    $authManager->assign($role, $user->getId());

    foreach (common\modules\admin\rbac\Permission::getConstants() as $permissionName) {
        $permission = $authManager->createPermission($permissionName);
        if ($permissionName != common\modules\admin\rbac\Permission::ACCESS_TO_ADMIN_DASHBOARD
            && $permissionName != common\modules\admin\rbac\Permission::MANAGE_USERS
            && $permissionName != common\modules\admin\rbac\Permission::MANAGE_ROLES
            && $permissionName != common\modules\admin\rbac\Permission::MANAGE_SITES
            && $permissionName != common\modules\admin\rbac\Permission::READ_LOG
        ) {
            $permission->ruleName = $siteRule->name;
        }
        $authManager->add($permission);
        $authManager->addChild($role, $permission);
    }

    $application = new yii\web\Application($config);
    $application->request->enableCsrfValidation = false;
    $application->run();

    $identity = common\models\User::findOne(['username' => $admin_username]);
    if ($identity != null) {
        Yii::$app->user->login($identity);

        $permalink = new common\models\Permalink();
        $permalink->name = '/';
        $permalink->route = $config['params']['postRoute'];
        $permalink->site_id = $site->id;
        $permalink->save();

        $page = new common\models\Post();
        $page->type = common\models\Post::TYPE_PAGE;
        $page->status = common\models\Post::STATUS_ACTIVE;
        $page->permalink_id = $permalink->id;
        $page->title = "Home page";
        $page->content = "This is the homepage content.";
        $page->site_id = $site->id;
        $page->save();

        $pageMeta = new common\models\PostMeta();
        $pageMeta->type = common\models\PostMeta::TYPE_META;
        $pageMeta->name = common\models\PostMeta::TITLE_TAG;
        $pageMeta->value = "My site";
        $pageMeta->parent_id = $page->id;
        $pageMeta->site_id = $site->id;
        $pageMeta->post_meta_order = 1;
        $pageMeta->save();

        $permalink = new common\models\Permalink();
        $permalink->name = 'about';
        $permalink->route = $config['params']['postRoute'];
        $permalink->site_id = $site->id;
        $permalink->save();

        $pageAbout = new common\models\Post();
        $pageAbout->type = common\models\Post::TYPE_PAGE;
        $pageAbout->status = common\models\Post::STATUS_ACTIVE;
        $pageAbout->permalink_id = $permalink->id;
        $pageAbout->title = "About";
        $pageAbout->content = "This is a page with some basic information about this site.";
        $pageAbout->site_id = $site->id;
        $pageAbout->save();

        $categoryHome = new common\models\PostMeta();
        $categoryHome->type = common\models\PostMeta::TYPE_CATEGORY;
        $categoryHome->name = "Home page";
        $categoryHome->value = "/";
        $categoryHome->description = "";
        $categoryHome->site_id = $site->id;
        $categoryHome->post_meta_order = 1;
        $categoryHome->save();

        $categoryNews = new common\models\PostMeta();
        $categoryNews->type = common\models\PostMeta::TYPE_CATEGORY;
        $categoryNews->name = "News";
        $categoryNews->value = "news";
        $categoryNews->description = "";
        $categoryNews->site_id = $site->id;
        $categoryNews->post_meta_order = 2;
        $categoryNews->save();

        $tag = new common\models\PostMeta();
        $tag->type = common\models\PostMeta::TYPE_TAG;
        $tag->name = "Post tag";
        $tag->value = "some-topic";
        $tag->description = "";
        $tag->site_id = $site->id;
        $tag->post_meta_order = 1;
        $tag->save();

        $permalink = new common\models\Permalink();
        $permalink->name = 'some-post-home-page';
        $permalink->route = $config['params']['postRoute'];
        $permalink->site_id = $site->id;
        $permalink->save();

        $post = new common\models\Post();
        $post->type = common\models\Post::TYPE_POST;
        $post->status = common\models\Post::STATUS_ACTIVE;
        $post->permalink_id = $permalink->id;
        $post->date = date('Y-m-d H:i:s');
        $post->title = "Some post";
        $post->content = "This is the post content.";
        $post->site_id = $site->id;
        $post->save();

        $postMetaRelationship = new common\models\PostMetaRelationship();
        $postMetaRelationship->post_id = $post->id;
        $postMetaRelationship->post_meta_id = $categoryHome->id;
        $postMetaRelationship->save();

        $postMetaRelationship = new common\models\PostMetaRelationship();
        $postMetaRelationship->post_id = $post->id;
        $postMetaRelationship->post_meta_id = $tag->id;
        $postMetaRelationship->save();

        $permalink = new common\models\Permalink();
        $permalink->name = 'some-news';
        $permalink->route = $config['params']['postRoute'];
        $permalink->site_id = $site->id;
        $permalink->save();

        $post = new common\models\Post();
        $post->type = common\models\Post::TYPE_POST;
        $post->status = common\models\Post::STATUS_ACTIVE;
        $post->permalink_id = $permalink->id;
        $post->date = date('Y-m-d H:i:s');
        $post->title = "Some news";
        $post->content = "This is the news content.";
        $post->site_id = $site->id;
        $post->save();

        $postMetaRelationship = new common\models\PostMetaRelationship();
        $postMetaRelationship->post_id = $post->id;
        $postMetaRelationship->post_meta_id = $categoryNews->id;
        $postMetaRelationship->save();

        $menu = new common\models\Menu();
        $menu->name = "Top menu";
        $menu->site_id = $site->id;
        $menu->save();

        $menuItem = new common\models\MenuItem();
        $menuItem->name = $categoryNews->name;
        $menuItem->type = common\models\MenuItem::TYPE_CATEGORY;
        $menuItem->value = $categoryNews->id;
        $menuItem->menu_item_order = 0;
        $menuItem->menu_id = $menu->id;
        $menuItem->save();

        $menuItem = new common\models\MenuItem();
        $menuItem->name = $pageAbout->title;
        $menuItem->type = common\models\MenuItem::TYPE_PAGE;
        $menuItem->value = $pageAbout->id;
        $menuItem->menu_item_order = 1;
        $menuItem->menu_id = $menu->id;
        $menuItem->save();

        Yii::$app->user->logout();
    }
}

function  formatDSN($typeOfDatabase, $dbName, $host)
{
    if ($typeOfDatabase == "mysql" || $typeOfDatabase == "pgsql") {
        return $typeOfDatabase . ':host=' . $host . ';dbname=' . $dbName;
    }
    throw new Exception("Unknown type of database.");
}

?>




