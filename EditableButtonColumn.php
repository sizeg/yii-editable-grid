<?php

/**
 * EditableButtonColumn class file.
 *
 * @author Dyomin Dmitry <sizemail@gmail.com>
 * @link http://size.perm.ru/yii-editable-grid
 * @copyright 2014 SiZE
 */
class EditableButtonColumn extends CButtonColumn {

	/**
	 * @var string The label for the remove row button. Defaults to "Delete".
	 * Note that the label will not be HTML-encoded when rendering.
	 */
	public $removeRowButtonLabel;
	
	/**
	 * @var string The image URL for the remove row button. If not set, an integrated image will be used.
	 * You may set this property to be false to render a text link instead.
	 */
	public $removeRowButtonImageUrl;
	
	/**
	 * @var string a PHP expression that is evaluated for every remove row button and whose result is used
	 * as the URL for the remove row button. In this expression, you can use the following variables:
	 * <ul>
	 *   <li><code>$row</code> the row number (zero-based)</li>
	 *   <li><code>$data</code> the data model for the row</li>
	 *   <li><code>$this</code> the column object</li>
	 * </ul>
	 * The PHP expression will be evaluated using {@link evaluateExpression}.
	 *
	 * A PHP expression can be any PHP code that has a value. To learn more about what an expression is,
	 * please refer to the {@link http://www.php.net/manual/en/language.expressions.php php manual}.
	 */
	public $removeRowButtonUrl='"#"';

	/**
	 * @var array the HTML options for the remove row button tag.
	 */
	public $removeRowButtonOptions = array( 'class' => 'removeRow' );

	/**
	 * @var string the confirmation message to be displayed when removeRow button is clicked.
	 * By setting this property to be false, no confirmation message will be displayed.
	 * This property is used only if <code>$this->buttons['removeRow']['click']</code> is not set.
	 */
	public $removeRowConfirmation;

	/**
	 * @var string a javascript function that will be invoked after the removing row.
	 * This property is used only if <code>$this->buttons['removeRow']['click']</code> is not set.
	 */
	public $afterRemoveRow;

	/**
	 * @var string The label for the restore row button. Defaults to "Restore".
	 * Note that the label will not be HTML-encoded when rendering.
	 */
	public $restoreRowButtonLabel;

	/**
	 * @var string The image URL for the restore row button. If not set, an integrated image will be used.
	 * You may set this property to be false to render a text link instead.
	 */
	public $restoreRowButtonImageUrl;

	/**
	 * @var array the HTML options for the restore row button tag.
	 */
	public $restoreRowButtonOptions = array( 'class' => 'restoreRow' );

	/**
	 * @var string a javascript function that will be invoked after the restoring row.
	 */
	public $afterRestoreRow;
	
	/**
	 * @var string the template that is used to render the content in each data cell.
	 * (@see CButtonColumn.template)
	 */
	public $template = '{removeRow}';

	/**
	 * Initializes the default buttons (removeRow).
	 */
	protected function initDefaultButtons(){
		parent::initDefaultButtons();

		if ( $this->removeRowButtonLabel === null ) {
			$this->removeRowButtonLabel = Yii::t( 'zii', 'Delete' );
		}

		if ( $this->removeRowConfirmation === null ) {
			$this->removeRowConfirmation = Yii::t( 'zii', 'Are you sure you want to delete this item?' );
		}
		
		if ( $this->grid->restoreDeletedRows ) {
			if ( $this->restoreRowButtonLabel === null ) {
				$this->restoreRowButtonLabel = Yii::t( 'editablegrid.editablegrid', 'Restore' );
			}
		}

		$id = 'removeRow';

		$button = array(
			'label' => $this->{$id.'ButtonLabel'},
			'url' => $this->{$id.'ButtonUrl'},
			'imageUrl' => $this->{$id.'ButtonImageUrl'},
			'options' => $this->{$id.'ButtonOptions'},
		);

		if ( isset( $this->buttons[ $id ] ) ) {
			$this->buttons[ $id ] = array_merge( $button, $this->buttons[ $id ] );
		} else {
			$this->buttons[ $id ] = $button;
		}

		if ( !isset( $this->buttons[ $id ][ 'click' ] ) ) {
			if ( is_string( $this->{$id.'Confirmation'} ) ) {
				$confirmation = "if(!confirm(".CJavaScript::encode( $this->{$id.'Confirmation'} ).")) return false;";
			} else {
				$confirmation = '';
			}
			/*
			if ( Yii::app()->request->enableCsrfValidation ) {
				$csrfTokenName = Yii::app()->request->csrfTokenName;
				$csrfToken = Yii::app()->request->csrfToken;
				$csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
			} else {
				$csrf = '';
			}
			*/

			if ( $this->afterRemoveRow === null ) {
				$this->afterRemoveRow = 'function(){}';
			}

			// Allow to restore deleted rows
			if ( $this->grid->restoreDeletedRows ) {
				if ( $this->afterRestoreRow === null ) {
					$this->afterRestoreRow = 'function(){}';
				}

				if ( !empty( $this->restoreRowButtonImageUrl ) ) {
					$restore_link = CHtml::link( CHtml::image( $this->restoreRowButtonImageUrl, $this->restoreRowButtonLabel ), '#', $this->removeRowButtonOptions );
				} else {
					$restore_link = CHtml::link( $this->restoreRowButtonLabel, '#', $this->restoreRowButtonOptions );
				}
				$label = isset( $button[ 'label' ] ) ? $button[ 'label' ] : $id;

				$columns = sizeof( $this->grid->columns );
				$this->buttons[ $id ][ 'click' ] = <<<EOD
function(){
	$confirmation
	var th = this,
		afterDelete = {$this->afterRemoveRow},
		tr = $(this).parents("tr");

	tr.find('input,select').each(function(){
		$(this).prop('disabled',true);
	});
	tr.after('<tr><td colspan="{$columns}">{$restore_link}</td></tr>');
	tr.hide();
	afterDelete(th);
	return false;
}
EOD;
				
				Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id.'-restoreRow',"
jQuery(document).on('click','#{$this->grid->id} a.restoreRow',function(){
	var th = this,
		tr = $(this).parents('tr'),
		real_tr = tr.prev('tr'),
		afterRestore = {$this->afterRestoreRow};

	tr.remove();
	real_tr.show();
	real_tr.find('input,select').each(function(){
		$(this).prop('disabled',false);
	});
	afterRestore(th);
});
			");
			} else {
				$this->buttons[ $id ][ 'click' ] = <<<EOD
function(){
	$confirmation
	var th = this,
		afterDelete = {$this->afterRemoveRow};
	$(this).parents("tr").remove();
	afterDelete(th);
	return false;
}
EOD;
			}
		}
	}
	
}
