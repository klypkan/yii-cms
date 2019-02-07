<?php
namespace common\modules\admin\components;

use Yii;
use yii\base\Widget;
use yii\i18n\Formatter;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Inflector;
use yii\widgets\BaseListView;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\data\ActiveDataProvider;
use common\modules\admin\Module;
use common\modules\admin\assets\GridViewAsset;

class GridView extends BaseListView
{
    public $model;
    public $headerRowOptions = ['class' => 'entity-list-table-header'];
    public $filter = [];
    public $showFilter = false;
    public $formatter;
    public $columns = [];
    public $dataColumnClass;
    public $editAction = "edit";
    public $deleteAction = "delete";
    public $editActionsEnabled = true;
    public $pageSize = 20;
    public $defaultOrder = [];
    public $layout = "{items}\n{pager}";

    public function init()
    {
        $this->initColumns();

        $this->initFilter();

        $query = $this->model->find();
        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
            'sort' => [
                'defaultOrder' => $this->defaultOrder
            ],
        ]);

        foreach ($this->filter as $filterItem) {
            if (!empty($filterItem->operator) && $filterItem->operator != "NoSet") {
                $this->model[$filterItem->attribute] = $filterItem->value;
            }
        }

        if ($this->model->validate()) {
            foreach ($this->filter as $filterItem) {
                if (!empty($filterItem->operator) && $filterItem->operator != "NoSet") {
                    $query->andFilterWhere([$this->geOperationQuery($filterItem->operator), $filterItem->attribute, $filterItem->value]);
                }
            }
        } else {
            $errors = $this->model->errors;
            foreach ($errors as $key => $value) {
                foreach ($this->filter as $i => $filterItem) {
                    if ($key == $filterItem->attribute) {
                        $filterItem->errors = $value;
                        $this->filter[$i] = $filterItem;
                        break;
                    }
                }

            }
        }

        parent::init();

        if ($this->formatter == null) {
            $this->formatter = Yii::$app->getFormatter();
        } elseif (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
        if (!$this->formatter instanceof Formatter) {
            throw new InvalidConfigException('The "formatter" property must be either a Format object or a configuration array.');
        }
        $this->showOnEmpty = true;
    }

    public function run()
    {
        $view = $this->getView();
        GridViewAsset::register($view);
        parent::run();
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItems()
    {
        $toolbar = $this->renderToolbar();
        $filter = $this->renderFilter();
        $tableHeader = $this->renderTableHeader();
        $tableBody = $this->renderTableBody();
        $content = array_filter([
            $tableHeader,
            $tableBody,
        ]);

        return $toolbar . $filter . Html::tag('table', implode("\n", $content), ['id' => 'entity-list-table', 'class' => 'table table-bordered']);
    }

    /**
     * Renders the toolbar.
     * @return string the rendering result.
     */
    protected function renderToolbar()
    {
        $controller = Yii::$app->controller;
        $editUrlParameters = [$controller->id . '/' . $this->editAction];
        $deleteUrlParameters = [$controller->id . '/' . $this->deleteAction];
        $queryParams = Yii::$app->request->getQueryParams();
        foreach ($queryParams as $key => $value) {
            $editUrlParameters[$key] = $value;
            $deleteUrlParameters[$key] = $value;
        }
        $editUrl = Url::to($editUrlParameters);
        $addBtn = Html::tag('button', '<i class="fa fa-plus"></i>', ['id' => 'add-entity', 'class' => 'btn btn-default', 'type' => 'button', 'title' => Module::t('app', 'Create'), 'data-edit-url' => $editUrl]);
        $addBtnGroup = Html::tag('div', $addBtn, ['class' => 'btn-group']);

        $deleteUrl = Url::to($deleteUrlParameters);
        $deleteBtn = Html::tag('button', '<i class="fa fa-remove"></i>', ['id' => 'delete-entity', 'class' => 'btn btn-default', 'type' => 'button', 'title' => Module::t('app', 'Delete'), 'data-delete-url' => $deleteUrl]);
        $deleteBtnGroup = Html::tag('div', $deleteBtn, ['class' => 'btn-group']);

        $filterBtnGroup = '';
        if (!empty($this->filter)) {
            $filterBtn = Html::tag('button', '<i class="fa fa-filter"></i>', ['id' => 'filter-entity-list', 'class' => 'btn btn-default btn-filter', 'type' => 'button', 'title' => Module::t('app', 'Filter')]);
            $filterBtnGroupOptions = ['class' => 'btn-group'];
            if ($this->showFilter) {
                $filterBtnGroupOptions['class'] = 'btn-group active';
            }
            $filterBtnGroup = Html::tag('div', $filterBtn . '<div class="btn-filter-active"></div>', $filterBtnGroupOptions);
        }

        $content = array();
        if ($this->editActionsEnabled) {
            $content[] = $addBtnGroup;
            $content[] = $deleteBtnGroup;
        }
        $content[] = $filterBtnGroup;

        $toolbar = '<div id="entity-list-error" class="alert alert-danger hidden" role="alert"></div>' . Html::tag('div', implode("\n", $content), ['class' => 'btn-toolbar entity-list-toolbar']);
        return $toolbar;
    }

    protected function renderFilter()
    {
        $filter = '';
        if (!empty($this->filter)) {
            $filterTableContent = '';
            foreach ($this->filter as $filterItem) {
                if ($filterItem->visible) {
                    $filterTableContent = $filterTableContent . $this->renderFilterItem($filterItem);
                }
            }
            $filterTableContent = $filterTableContent . '</tr>
                <td colspan="3" class="filter-entity-list-btn-group">
                   <div class="btn-group">
                      <button id="apply-filter-btn" class="btn btn-primary" type="button">' . Module::t('app', 'Apply') . '</button>
                      <button id="clear-filter-btn" class="btn btn-default" type="button">' . Module::t('app', 'Clear') . '</button>
                      <button id="close-filter-btn" class="btn btn-default" type="button">' . Module::t('app', 'Close') . '</button>
                   </div>
                </td>
            </tr>';
            $filterTable = Html::tag('table', $filterTableContent, ['class' => 'table']);

            $request = Yii::$app->request;
            $url = Url::to($request->url);
            $pos = strrpos($url, '?');
            $url = $pos < 0 ? $pos : substr($url, 0, $pos);
            $queryParams = $request->getQueryParams();
            $formContent = '';
            foreach ($queryParams as $key => $value) {
                if ($key != "filter" && $key != "show-filter") {
                    $formContent = $formContent . '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
                }
            }
            $formContent = $formContent . '<input type="hidden" name="filter" value="" />';
            $formContent = $formContent . '<input type="hidden" name="show-filter" value="' . ($this->showFilter ? "true" : "false") . '" />';
            $formContent = $formContent . $filterTable;
            $form = Html::tag('form', $formContent, ['action' => $url, 'method' => 'get']);
            $filterOptions = ['id' => 'entity-list-filter', 'class' => 'btn-toolbar entity-list-filter hidden'];
            if ($this->showFilter) {
                $filterOptions['class'] = 'btn-toolbar entity-list-filter';
            }
            $filter = Html::tag('div', $form, $filterOptions);
        }
        return $filter;
    }

    protected function renderFilterItem($filterItem)
    {
        $filterItemRowContent = Html::tag('td', Html::tag('label', $filterItem->label));
        $dropDownOptions = ['class' => 'form-control filter-item-operator-selector'];
        if ($filterItem->readOnly) {
            $dropDownOptions['disabled'] = 'disabled';
        }
        $filterItemRowContent = $filterItemRowContent . Html::tag('td', Html::dropDownList('', $filterItem->operator, $this->getOperationList($filterItem->operators), $dropDownOptions));

        $filterCellOptions = [];
        if (!empty($filterItem->errors)) {
            $filterCellOptions['class'] = 'has-error';
        }
        $filterItemRowContent = $filterItemRowContent . Html::tag('td', $this->renderFilterItemValue($filterItem), $filterCellOptions);

        $filterRowOptions = ['class' => 'filter-item'];
        return Html::tag('tr', $filterItemRowContent, $filterRowOptions);
    }

    private function getOperationList($operators)
    {
        $operationList = [];
        foreach ($operators as $operator) {
            $operationList[$operator] = Module::t('operator', $this->getDefaultOperationText($operator));
        }
        return $operationList;
    }

    private function geOperationQuery($operator)
    {
        switch ($operator) {
            case "NoSet":
                return "";
            case "Equal":
                return "=";
            case "NotEqual":
                return "<>";
            case"GreaterThan":
                return ">";
            case "GreaterThanOrEqual":
                return ">=";
            case "LessThan":
                return "<";
            case "LessThanOrEqual":
                return "<=";
            case "Like":
                return "like";
        }
        return "";
    }

    protected function  getDefaultOperators($attribute)
    {
        return ['NoSet', 'Equal', 'NotEqual'];
    }

    protected function getDefaultOperationText($operator)
    {
        switch ($operator) {
            case "NoSet":
                return "No set";
            case "Equal":
                return "Equal";
            case "NotEqual":
                return "Not equal";
            case"GreaterThan":
                return "Greater than";
            case "GreaterThanOrEqual":
                return "Greater than or equal";
            case "LessThan":
                return "Less than";
            case "LessThanOrEqual":
                return "Less than or equal";
            case "Like":
                return "Like";
        }
        return "";
    }

    protected function renderFilterItemValue($filterItem)
    {
        if ($filterItem->valueHandler instanceof \Closure) {
            return call_user_func($filterItem->valueHandler, $filterItem, $this->model);
        }

        $options = ['class' => 'form-control', 'data-name' => $filterItem->attribute];
        if ($filterItem->readOnly) {
            $options["disabled"] = "";
        }
        if (!empty($filterItem->errors)) {
            $options['title'] = implode(" ", $filterItem->errors);
            $options['data-toggle'] = 'tooltip';
        }
        return Html::input('text', null, $filterItem->value, $options);
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    protected function renderTableHeader()
    {
        $cells = [];
        if ($this->editActionsEnabled) {
            $cells[] = Html::tag('th', '<input id="select-entity-list" type="checkbox" />', ['class' => 'entity-list-table-header entity-list-table-header-selector']);
        }
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        return "<thead>\n" . $content . "\n</thead>";
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    protected function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            $rows[] = $this->renderTableRow($model, $key, $index);
        }

        if (empty($rows)) {
            $colspan = count($this->columns) + 1;

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        } else {
            return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
        }
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    protected function renderTableRow($model, $key, $index)
    {
        $cells = [];
        if ($this->editActionsEnabled) {
            $cells[] = Html::tag('td', '<input type="checkbox" class="entity-selector" />', ['class' => 'entity-list-item-selector']);
        }
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }

        $options = ['class' => 'entity-list-item' . ($this->editActionsEnabled ? ' entity-list-item-editable' : '')];
        $options['data-key'] = is_array($key) ? json_encode($key) : (string)$key;
        $controller = Yii::$app->controller;
        $editUrlParameters = [$controller->id . '/' . $this->editAction];
        $editUrlParameters['id'] = is_array($key) ? json_encode($key) : (string)$key;
        $queryParams = Yii::$app->request->getQueryParams();
        foreach ($queryParams as $key => $value) {
            $editUrlParameters[$key] = $value;
        }
        $editUrl = Url::to($editUrlParameters);
        $options['data-edit-url'] = Url::to($editUrl);

        return Html::tag('tr', implode('', $cells), $options);
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::className(),
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    protected function initFilter()
    {
        if (empty($this->filter)) {
            foreach ($this->columns as $i => $column) {
                if (is_string($column)) {
                    if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $column, $matches)) {
                        throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
                    }

                    $column = Yii::createObject(array_merge([
                        'class' => FilterItem::className(),
                        'attribute' => $matches[1],
                        'label' => isset($matches[5]) ? $matches[5] : Inflector::camel2words($matches[1]),
                        'operators' => $this->getDefaultOperators($column['attribute'])
                    ]));
                } else {
                    $column = Yii::createObject(array_merge([
                        'class' => FilterItem::className(),
                        'attribute' => $column->attribute,
                        'label' => isset($column->label) ? $column->label : Inflector::camel2words($column->attribute),
                        'operators' => $this->getDefaultOperators($column->attribute)
                    ]));
                }
                $this->filter[$i] = $column;
            }
        } else {
            foreach ($this->filter as $i => $filterItem) {
                $filterItem = Yii::createObject(array_merge([
                    'class' => FilterItem::className(),
                    'attribute' => $filterItem['attribute'],
                    'label' => isset($filterItem['label']) ? $filterItem['label'] : $filterItem['attribute'],
                    'operator' => isset($filterItem['operator']) ? $filterItem['operator'] : null,
                    'operators' => isset($filterItem['operators']) ? $filterItem['operators'] : $this->getDefaultOperators($filterItem['attribute']),
                    'value' => isset($filterItem['value']) ? $filterItem['value'] : null,
                    'valueHandler' => isset($filterItem['valueHandler']) ? $filterItem['valueHandler'] : null,
                    'readOnly' => isset($filterItem['readOnly']) ? $filterItem['readOnly'] : false,
                    'visible' => isset($filterItem['visible']) ? $filterItem['visible'] : true,
                ]));
                $this->filter[$i] = $filterItem;
            }
        }

        $request = Yii::$app->request;
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams["filter"])) {
            $queryFilter = json_decode($queryParams["filter"], true);
            foreach ($this->filter as $i => $filterItem) {
                if (!$filterItem->readOnly) {
                    foreach ($queryFilter as $queryFilterItem) {
                        if ($queryFilterItem["name"] == $filterItem->attribute) {
                            $filterItem->operator = $queryFilterItem["opr"];
                            $filterItem->value = $queryFilterItem["val"];
                            $this->filter[$i] = $filterItem;
                            break;
                        }
                    }
                }
            }
        }
        if (isset($queryParams["show-filter"]) && $queryParams["show-filter"] == "true") {
            $this->showFilter = true;
        }
    }

    /**
     * Creates a [[DataColumn]] object based on a string in the format of "attribute:format:label".
     * @param string $text the column specification string
     * @return DataColumn the column instance
     * @throws InvalidConfigException if the column specification is invalid
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }

        return Yii::createObject([
            'class' => $this->dataColumnClass ?: DataColumn::className(),
            'grid' => $this,
            'attribute' => $matches[1],
            'format' => isset($matches[3]) ? $matches[3] : 'text',
            'label' => isset($matches[5]) ? $matches[5] : null,
        ]);
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        $models = $this->dataProvider->getModels();
        $model = reset($models);
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                $this->columns[] = $name;
            }
        }
    }
}