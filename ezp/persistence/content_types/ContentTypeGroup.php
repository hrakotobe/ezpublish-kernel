<?php
namespace ezp\persistence\content_types;
/**
 * @access public
 * @author root
 * @package ezp.persistence.content_types
 */
class ContentTypeGroup extends ContentTypeBase 
{
	/**
	 * @AssociationType ezp.persistence.content_types.ContentType
	 * @AssociationMultiplicity 0..*
	 * @AssociationKind Aggregation
	 */
	public $unnamed_ContentType_ = array();
}
?>