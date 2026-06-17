<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Traits\HasFields;

/**
 * Class ContactPerson
 * @package SURF\PostTypes
 */
class ContactPerson extends BasePost
{

	use Registers, HasFields;

	protected static string $postType = 'surf-contact-person';

	public const FIELD_FIRST_NAME    = 'first_name';
	public const FIELD_LAST_NAME     = 'last_name';
	public const FIELD_EMAIL_ADDRESS = 'email_address';
	public const FIELD_DESCRIPTION   = 'description';
	public const FIELD_PICTURE       = 'picture';

	/**
	 * @return string[]
	 */
	public static function getArgs(): array
	{
		return [
			'menu_icon' => 'dashicons-editor-help',
			'public'    => false,

		];
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'contact-person', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Contact person', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Contact persons', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		return [
			'key'    => 'contact_person_group_settings',
			'title'  => _x( 'Contact person settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'      => 'field_contact_person_first_name',
					'name'     => static::FIELD_FIRST_NAME,
					'label'    => _x( 'First name', 'admin', 'wp-surf-theme' ),
					'type'     => 'text',
					'required' => 1,
				],
				[
					'key'      => 'field_contact_person_last_name',
					'name'     => static::FIELD_LAST_NAME,
					'label'    => _x( 'Last name', 'admin', 'wp-surf-theme' ),
					'type'     => 'text',
					'required' => 1,
				],
				[
					'key'   => 'field_contact_person_email_address',
					'name'  => static::FIELD_EMAIL_ADDRESS,
					'label' => _x( 'Email address', 'admin', 'wp-surf-theme' ),
					'type'  => 'email',
				],
				[
					'key'   => 'field_contact_person_description',
					'name'  => static::FIELD_DESCRIPTION,
					'label' => _x( 'Description', 'admin', 'wp-surf-theme' ),
					'type'  => 'textarea',
				],
				[
					'key'           => 'field_contact_person_picture',
					'name'          => static::FIELD_PICTURE,
					'label'         => _x( 'Picture', 'admin', 'wp-surf-theme' ),
					'type'          => 'image',
					'preview_size'  => 'thumbnail',
					'return_format' => 'id',
					'instructions'  => _x( 'The picture will be cropped to a square. Make sure the subject is centered.', 'admin', 'wp-surf-theme' ),
				],
			],
		];
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	public function has( string $property ): bool
	{
		return method_exists( $this, $property ) && !empty( $this->$property() );
	}

	/**
	 * @return string
	 */
	public function firstName(): string
	{
		return $this->getMeta( static::FIELD_FIRST_NAME ) ?: '';
	}

	/**
	 * @return string
	 */
	public function lastName(): string
	{
		return $this->getMeta( static::FIELD_LAST_NAME ) ?: '';
	}

	/**
	 * @return string
	 */
	public function fullName(): string
	{
		return trim( implode( ' ', [ $this->firstName(), $this->lastName() ] ) );
	}

	/**
	 * @return string
	 */
	public function description(): string
	{
		return $this->getMeta( static::FIELD_DESCRIPTION ) ?: '';
	}

	/**
	 * @return string
	 */
	public function emailAddress(): string
	{
		return $this->getMeta( static::FIELD_EMAIL_ADDRESS ) ?: '';
	}

	/**
	 * @return string
	 */

	public function emailAddressUrl(): string
	{
		return 'mailto:' . $this->emailAddress();
	}

	/**
	 * @return int
	 */
	public function pictureId(): int
	{
		return intval( $this->getMeta( static::FIELD_PICTURE, 0 ) );
	}

	/**
	 * @param string $size
	 * @param array $attrs
	 * @return string
	 */
	public function pictureMarkup( string $size = 'avatar', array $attrs = [] ): string
	{
		return wp_get_attachment_image( $this->pictureId(), $size, false, $attrs );
	}

}
