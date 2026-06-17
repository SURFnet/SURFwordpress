<?php

namespace SURF\Plugins\WpMailSmtp;

use SURF\Plugins\Plugin;
use WPMailSMTP\Helpers\Crypto;

/**
 * Class WpMailSmtp
 * @package SURF\Plugins\WpMailSmtp
 */
class WpMailSmtp extends Plugin
{

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'WP Mail SMTP';
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'wp_mail_smtp.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'wp-mail-smtp';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/WpMailSmtp/' . $this->getSlug() . '.zip' );
	}

	/**
	 * @return void
	 */
	public function afterActivation(): void
	{
		$key = Crypto::get_secret_key( true );
		update_option( 'wp_mail_smtp', [
			"smtp"       => [
				"autotls"    => true,
				"host"       => "mta.ia.surf.nl",
				"encryption" => "tls",
				"port"       => 26,
				"user"       => "",
				"pass"       => Crypto::encrypt( '', $key ),
				"auth"       => false,
			],
			"general"    => [
				"summary_report_email_disabled" => true,
			],
			"mail"       => [
				"from_email"       => "no-reply@surf.nl",
				"from_name"        => get_bloginfo( 'name' ),
				"mailer"           => "smtp",
				"from_email_force" => false,
				"from_name_force"  => false,
				"return_path"      => false,
			],
			"sendlayer"  => [
				"api_key" => "",
			],
			"smtpcom"    => [
				"api_key" => "",
				"channel" => "",
			],
			"sendinblue" => [
				"api_key" => "",
				"domain"  => "",
			],
			"gmail"      => [
				"client_id"     => "",
				"client_secret" => "",
			],
			"mailgun"    => [
				"api_key" => "",
				"domain"  => "",
				"region"  => "US",
			],
			"postmark"   => [
				"server_api_token" => "",
				"message_stream"   => "",
			],
			"sendgrid"   => [
				"api_key" => "",
				"domain"  => "",
			],
			"sparkpost"  => [
				"api_key" => "",
				"region"  => "US",
			],
		] );
	}

}
