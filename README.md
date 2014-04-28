yii-editable-grid
=================

Yii editable grid

Usage in view:

```php
// Import widget
Yii::import('application.widgets.yii-editable-grid.*');

// Makes row template
$row_template = '
	<tr>
		<td>'.CHtml::textField('TestModel[{gridNum}][{rowNum}][title]', '', array('size'=>40,'maxlength'=>255)).'</td>
		<td>'.CHtml::textField('TestModel[{gridNum}][{rowNum}][price]', '', array('size'=>5,'maxlength'=>15)).'</td>
		<td>'.CHtml::textField('TestModel[{gridNum}][{rowNum}][quantity]', '', array('size'=>5,'maxlength'=>8)).'</td>
		<td>'.CHtml::dropDownList('TestModel[{gridNum}][{rowNum}][color]', '', $colors_list, array('empty'=>'')).'</td>
		<td style="text-align: right;">0</td>
		<td class="button-column"><a class="removeRow" title="Delete" href="#">Delete</a></td>
	</tr>
';

// Init your own data provider
$dataProvider = new CArrayDataProvider( TestModel::model()->findAll() );

// Use widget
$this->widget('EditableGrid', array(
	'dataProvider' => $dataProvider,
	'template' => '{items} {buttonCreateRow}',
	'rowTemplate' => $row_template,
	'columns' => array(
		array(
			'class' => 'EditableGridColumn',
			'header' => 'Title',
			'name' => '[{gridNum}][{rowNum}]title',
			'tag' => 'textField',
			'tagHtmlOptions' => array(
				'size' => '40'
			)
		),
		array(
			'class' => 'EditableGridColumn',
			'header' => 'Price',
			'name' => '[{gridNum}][{rowNum}]price',
			'tag' => 'textField',
			'tagHtmlOptions' => array(
				'size' => '5'
			)
		),
		array(
			'class' => 'EditableGridColumn',
			'header' => 'Quantity',
			'name' => '[{gridNum}][{rowNum}]quantity',
			'tag' => 'textField',
			'tagHtmlOptions' => array(
				'size' => '5'
			)
		),
		array(
			'class' => 'EditableGridColumn',
			'header' => 'Color',
			'name' => '[{gridNum}][{rowNum}]color',
			'tag' => 'dropDownList',
			'tagData' => $colors_list,
			'tagHtmlOptions' => array(
				'empty' => ''
			)
		),
		array(
			'class' => 'EditableGridColumn',
			'header' => 'Total',
			'value' => '($data["price"] * $data["quantity"])',
			'htmlOptions' => array(
				'style' => 'text-align: right;'
			)
		),
		array(
			'class' => 'EditableButtonColumn',
		),
	),
));
```

##Demo
Please see **[size.perm.ru/yii-editable-grid/](http://size.perm.ru/yii-editable-grid/)**

