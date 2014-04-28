<?php

/**
 * EditableGridColumn class file.
 * Makes editable grid column.
 *
 * @author Dyomin Dmitry <sizemail@gmail.com>
 * @link http://size.perm.ru/yii-editable-grid
 * @copyright 2014 SiZE
 */
class EditableGridColumn extends CDataColumn {

	/**
	 * @var string Cell tag. Supported: textField, dropDownList
	 */
	public $tag;

	/**
	 * @var array For generating the list options (value=>display)
	 */
	public $tagData;

	/**
	 * @var array The HTML options for the cell tag.
	 */
	public $tagHtmlOptions = array();

	/**
	 * @var int Grid row counter
	 */
	private static $_prevRowNum;

	/**
	 * Renders the data cell content.
	 * This method evaluates {@link value} or {@link name} and renders the result.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent( $row, $data ){
		if ( $this->tag === null ) {
			parent::renderDataCellContent( $row, $data );
		} else {
			$is_model = ( $data instanceof CModel ) ? true : false;
			
			// Запишем идентификатор для существующей записи
			if ( self::$_prevRowNum !== $row ) {
				self::$_prevRowNum = $row;
				$primary_name = str_replace( array( '{gridNum}', '{rowNum}' ), array( $this->grid->getGridCounter(), $row ), $this->grid->primaryKey );
				$primary_real = preg_replace( '#.*\](.*)$#', '\\1', $this->grid->primaryKey );
				if ( isset( $data[ $primary_real ] ) ) {
					if ( $is_model ) {
						echo CHtml::activeHiddenField( $data, $primary_name, $this->grid->primaryKeyHtmlOptions );
					} else {
						echo CHtml::hiddenField( $primary_name, $data[ $primary_real ], $this->grid->primaryKeyHtmlOptions );
					}
				}
			}

			$real = preg_replace( '#.*\](.*)$#', '\\1', $this->name );
			$name = str_replace( array( '{gridNum}', '{rowNum}' ), array( $this->grid->getGridCounter(), $row ), $this->name );

			switch( $this->tag ){
				case 'textField':
					if ( $is_model ) {
						echo CHtml::activeTextField( $data, $name, $this->tagHtmlOptions );
					} else {
						echo CHtml::textField( $name, $data[ $real ], $this->tagHtmlOptions );
					}
				break;

				case 'dropDownList':
					if ( $is_model ) {
						echo CHtml::activeDropDownList( $data, $name, $this->tagData, $this->tagHtmlOptions );
					} else {
						echo CHtml::dropDownList( $name, $data[ $real ], $this->tagData, $this->tagHtmlOptions );
					}
				break;
			}
		}
	}

}
