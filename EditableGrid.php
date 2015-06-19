<?php
Yii::import('zii.widgets.grid.CGridView');

/**
 * EditableGrid class file.
 *
 * @author Demin Dmitry <sizemail@gmail.com>
 * @link http://size.perm.ru/yii-editable-grid
 * @copyright 2014 SiZE
 */
class EditableGrid extends CGridView {

	/**
	 * @var int Grids counter
	 */
	private static $_grid_counter = 0;

	/**
	 * @var int Manual grid number
	 */
	public $gridNum;

	/**
	 * @var string The label for the add row button. Defaults to "Add new row"
	 */
	public $buttonCreateRowLabel;

	/**
	 * @var string A javascript function that will be invoked after the removing row
	 */
	public $afterCreateRow;

	/**
	 * @var string A javascript function to be invoked when the button create row is clicked
	 */
	public $buttonCreateRowClick;

	/**
	 * @var array The HTML options for the create row button tag
	 */
	public $buttonCreateRowOptions = array(
		'class' => 'new-grid-row'
	);

	/**
	 * @var string The HTML template for the new row
	 */
	public $rowTemplate;

	/**
	 * @var string the HTML TextArea tag attribute ID, generated to get template
	 */
	protected $rowTemplateId;

	/**
	 * @var string Row primary key. Used to generate hidden field with primaryKey from DB
	 */
	public $primaryKey = '[{gridNum}][{rowNum}]id';

	/**
	 * @var array the HTML options for the hidden field primaryKey
	 */
	public $primaryKeyHtmlOptions = array();

	/**
	 * @var int the number of table body rows that can be selected
	 */
	public $selectableRows = 0;

	/**
	 * @var bool whether to restore deleted rows. Set this property to true to
	 * enable lazy delete.
	 */
	public $restoreDeletedRows = false;

	protected $markDeletedRowsInputId;

	public function init(){
		parent::init();
		
		self::$_grid_counter++;

		$this->rowTemplateId = 'row-template-'.$this->id;
		
		if ( strpos( $this->template, '{buttonCreateRow}' ) !== false ) {
			$this->initButtonCreateRow();
			if( !isset( $this->buttonCreateRowOptions['class'] ) ) {
				$this->buttonCreateRowOptions['class'] = 'buttonCreateRow';
			}
			if ( !( $this->buttonCreateRowClick instanceof CJavaScriptExpression ) ) {
				$this->buttonCreateRowClick = new CJavaScriptExpression( $this->buttonCreateRowClick );
			}
		}

		if ( $this->restoreDeletedRows ) {
			$this->markDeletedRowsInputId = 'mark-deleted-'.$this->id;
			$this->initRestoreDeletedRowsInput();
		}
	}

	/**
	 * @return int
	 */
	public function getGridCounter(){
		if ( $this->gridNum === null ) {
			return self::$_grid_counter;
		} else {
			return $this->gridNum;
		}
	}

	public function initButtonCreateRow(){
		if ( $this->afterCreateRow === null ) {
			$this->afterCreateRow = 'function(){}';
		}

		$class = '';
		// @todo rowCssClassExpression support (@see CGridView.renderTableRow)
		if ( is_array( $this->rowCssClass ) && ($n = count( $this->rowCssClass ) ) > 0 ) {
			$class = "
	var rowCssClass = ".CJSON::encode( $this->rowCssClass ).",
		rowClass = rowCssClass[ ( rowNum % ".$n." ) ];
	
	if ( /<tr[^>]+class[^>]+/.test(rowTpl) ) {
		rowTpl = rowTpl.replace(/<tr([^>]+)class=(\"|')([^'\"]*)(\"|')/,'<tr$1class=$2$3 '+rowClass+'$4');
	} else {
		rowTpl = rowTpl.replace(/(<tr[^>]*)/,'$1 class=\"'+rowClass+'\"');
	}
			";
		}

		$this->buttonCreateRowClick = <<<EOD
function(){
	var th = this,
		afterCreateRow = {$this->afterCreateRow},
		rowTpl = $('#{$this->rowTemplateId}').val(),
		rowNum = $('#{$this->id} tbody tr').length,
		placeholders = [{p:'gridNum',v:'{$this->getGridCounter()}'},{p:'rowNum',v:rowNum}];
	
	{$class}

	for(var i=0; i<placeholders.length; i++){
		rowTpl = rowTpl.replace(new RegExp("\{"+placeholders[i].p+"\}", "g"), placeholders[i].v);
	}
	$('#{$this->id} tbody').append( rowTpl );

	afterCreateRow();
}
EOD;
	}

	public function registerClientScript(){
		parent::registerClientScript();
		if ( strpos( $this->template, '{buttonCreateRow}' ) !== false ) {
			$function = CJavaScript::encode( $this->buttonCreateRowClick );
			$class = preg_replace( '/\s+/', '.', $this->buttonCreateRowOptions[ 'class' ] );
			$js = "jQuery(document).on('click','#{$this->id} .{$class}',$function);";
			Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, $js);
		}
	}

	public function renderButtonCreateRow(){
		if ( $this->buttonCreateRowLabel === null ) {
			$this->buttonCreateRowLabel = Yii::t( 'editablegrid.editablegrid', 'Add new row' );
		}
		echo CHtml::button( $this->buttonCreateRowLabel, $this->buttonCreateRowOptions );

		echo CHtml::textArea( '', $this->rowTemplate, array( 'id' => $this->rowTemplateId, 'style' => 'display: none;' ) );
	}

	public function initRestoreDeletedRowsInput(){
		echo CHtml::hiddenField( 'delete', '', array( 'id' => $this->markDeletedRowsInputId ) );
	}

}
