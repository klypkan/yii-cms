# Yii-CMS

Yii-CMS is a content management system base on [Yii 2.0](http://www.yiiframework.com/).

# Features

- Creating and managing pages, posts, multilevel menus.
- SEO-friendly URLs.
- Edit permission management.
- Multi-site and multi-language support.
- Import data from WordPress.

# The system requirements

1. PHP 5.6 or higher, SwiftMailer requires PHP 7.
2. MySQL/MariaDB or PostgreSQL.

# Installation

1. Unpack the downloaded files to a Web-accessible folder.

   1.1. For Shared hosting environments copy all files except **yii, yii.bat, README.md**.

   1.2. For Self-Hosted environments copy only **common, console, site1, vendor** folders. Setting **site1/web** as the document root.

2. Open **your-site-name/install.php** in your browser. 

After the installation is successfully completed, you can delete **install.php, console**.

# For Developers

Yii-CMS is a standard Yii 2.0 project. The project structure similar to the Advanced Project Template. 
If you are proficient in Yii 2.0, you can use Yii-CMS for your needs. If not, start [here](https://www.yiiframework.com/doc/guide/2.0/).

# Documentation

# Configuring the RBAC permissions

The RBAC permissions are in the file **common/modules/admin/rbac/Permission.php**.<br />
Add a new permission as is shown below:
```php
const MY_PERMISSION = 'My permission description';
```
Get all permissions
```php
foreach (Permission::getConstants() as $key => $value) {

}
```

# GridView

The GridView widget is used to display data in a grid.

## Properties

**model** (required)

An [Active Record](http://www.yiiframework.com/doc-2.0/guide-db-active-record.html) model.

**filter** (optional)

The GridView filter configuration.
```php
'filter' => [
    [
        'attribute' => '',//(required)
        'label' => '',//(optional)
        'operators' => [],//(optional) ['NoSet', 'Equal', 'NotEqual', 'GreaterThan', 'GreaterThanOrEqual', 'LessThan', 'LessThanOrEqual', 'Like']
		'value' => null,//(optional)
		'value' => null,//(optional)
		'valueHandler'  => null,//(optional)
		'readOnly' => false,//(optional)
		'visible' => true,//(optional)
    ],
],
```

**showFilter** (optional, default: false)

**columns** (optional)

The [Yii 2 GridView column configuration](http://www.yiiframework.com/doc-2.0/yii-grid-gridview.html#$columns-detail)

**editAction** (optional, default: "edit")

**deleteAction** (optional, default: "delete")

**editActionsEnabled** (optional, default: true)

**pageSize** (optional, default: 20)

**defaultOrder** (optional)

The [default order](http://www.yiiframework.com/doc-2.0/yii-data-sort.html#$defaultOrder-detail)

## The example of using
```php
$model = new User();
$model->scenario = User::SCENARIO_SEARCH;
echo GridView::widget([
    'model' => $model,
    'columns' => [
        [
            'attribute' => 'username',
            'label' => Yii::t('app', 'Name'),
            'format' => 'text',
        ],
        [
            'attribute' => 'email',
            'format' => 'text'
        ],
    ],
    'defaultOrder' => [
        'username' => SORT_ASC,
    ]
]);
```


# License

Yii-CMS is provided under MIT License.

 
