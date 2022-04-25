<?php

#namespace WilokeTest\Controllers;


#use Elementor\Controls_Manager;
class RegistrationController
{
	public function __construct()
	{
		add_action('elementor/widgets/register', [$this, 'registerAddon'], 100);
		add_action('wp_enqueue_scripts', [$this, 'registerScripts']);

	}

	public function enqueueScriptsToElementor()
	{
		wp_enqueue_script(
			'wiloke-pricing-script',
			plugin_dir_url(__FILE__) . "../Source/script.js",
			['elementor-frontend'],
			WILOKE_CARD_VERSION,
			true
		);
	}

	public function registerScripts()
	{
		$aConfigs = json_decode(file_get_contents(plugin_dir_path(__FILE__) . '../Assets/New/config.json'), true);
		wp_register_style('wiloke-card-style',$aConfigs['css'] , [], WILOKE_CARD_VERSION);
		wp_register_style('wiloke-card-style-bundle.min',plugin_dir_url(__FILE__).'../Assets/swiper-bundle.min.css' , [], WILOKE_CARD_VERSION);
		//wp_enqueue_style( 'wiloke-card-style' );
		wp_register_script(
			'wiloke-card-script',
			$aConfigs['js'],
			[],
			WILOKE_CARD_VERSION,
			true
		);
		wp_register_script(
			'wiloke-card-script-1',
			plugin_dir_url(__FILE__).'../Assets/main.js',
			['elementor-frontend'],
			WILOKE_CARD_VERSION,
			true
		);
		wp_register_script(
			'wiloke-card-script-swiper-bundle',
			plugin_dir_url(__FILE__).'../Assets/swiper-bundle.min.js',
			[],
			WILOKE_CARD_VERSION,
			true
		);
	}

	public function registerAddon($oWidgetManager)
	{
		$oWidgetManager->register(new ElementorAddon());
	}
}