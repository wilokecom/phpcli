<?php
#namespace WilokeTest\Controllers;



use Elementor\Widget_Base;
use Timber\Timber;
#use WilokeOriginalNamespace\Share\TraitHandleAutoRenderSettingControls;


class ElementorAddon extends Widget_Base
{
	use TraitHandleAutoRenderSettingControls;

	public static $aSettings               = [];
	public        $aHighlightItemsSettings = [];
	public        $aBasicItemsSettings     = [];

	public function get_name()
	{
		return "Wiloke-Card-Table";
	}

	public function get_stack($with_common_controls = true)
	{
		return parent::get_stack(false);
	}

	public function get_title()
	{
		return esc_html__("Wiloke Card", WILOKE_CARD_NAMESPACE);
	}

	public function get_script_depends()
	{
		return ['wiloke-card-script','wiloke-card-script-swiper-bundle','wiloke-card-script-1'];
	}

	public function get_icon()
	{
		return 'eicon-call-to-action';
	}

	public function get_style_depends()
	{
		return ['wiloke-card-style','wiloke-card-style-bundle.min'];
	}

	public function get_categories()
	{
		return ['basic'];
	}

	public function get_keywords()
	{
		return ['card', 'wiloke', "table", "cardTable", "price", "card table", "list"];
	}

	private function parseItems($aSettings)
	{

		$aItems = [];
		$aDataFields = [];
		$aConfigs = $this->getDataConfigField();

		foreach ($aConfigs as $aSection) {
			if (!empty($aSection['fields'])) {
				foreach ($aSection['fields'] as $aFields) {
					if (is_array($aSettings[$aFields['id']])) {
						$aResult = [];
						$aNameField = array_map(function ($aItem) {
							return $aItem['name'];
						}, $aFields['fields']);
						foreach ($aSettings[$aFields['id']] as $aItemDataFields) {
							$aRawResult = [];
							foreach ($aNameField as $name) {
								if (is_array($aItemDataFields[$name])) {
									$aRawResult[$name] = $aItemDataFields[$name]['value'] ??$aItemDataFields[$name]['url']??"";
								} else {
									$aRawResult[$name] = $aItemDataFields[$name];
								}
							}
							$aResult[] = $aRawResult;

						}
						$aDataFields[$aFields['name']] = $aResult;
					} else {
						$aDataFields[$aFields['name']] = $aSettings[$aFields['id']];
					}
				}
			}
			$aItems[$aSection['name']] = $aDataFields;
			$aDataFields=[];
		}
	//echo json_encode($aItems);die();
		return $aItems;
	}

	protected function register_controls()
	{
		$aConfigs = $this->getDataConfigField();
		//var_dump($aConfigs);die();
		$this->handle($aConfigs, $this);
	}

	public function getDataConfigField(): array
	{
		return json_decode(file_get_contents(plugin_dir_path(__FILE__) . '../Assets/New/schema.json'), true);
	}

	protected function render()
	{

		Timber::$locations = WILOKE_CARD_VIEWS_PATH . 'src/Views';
		self::$aSettings = $this->get_settings_for_display();
		Timber::render(plugin_dir_path(__FILE__) . "../Views/section.twig", [
			"data" => $this->parseItems(self::$aSettings)
		]);
	}
}