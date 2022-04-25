<?php

#namespace WilokeTest\Controllers;


use Elementor\Controls_Manager;
use  Elementor\Repeater;
use Elementor\Widget_Base;


trait TraitHandleAutoRenderSettingControls
{
	public array $aConfigField = [];
	public array $convertTypeElementor
	                           = [
			'text'     => Controls_Manager::TEXT,
			'number'   => Controls_Manager::NUMBER,
			'textarea' => Controls_Manager::TEXTAREA,
			'wysiwyg'  => Controls_Manager::WYSIWYG,
			'code'     => Controls_Manager::CODE,
			'hidden'   => Controls_Manager::HIDDEN,
			'switcher' => Controls_Manager::SWITCHER,
			'toggle'   => Controls_Manager::POPOVER_TOGGLE,
			'group'    => Controls_Manager::POPOVER_TOGGLE,
			'select'   => Controls_Manager::SELECT,
			'select2'  => Controls_Manager::SELECT2,
			'choose'   => Controls_Manager::CHOOSE,
			'color'    => Controls_Manager::COLOR,
			'icons'    => Controls_Manager::ICONS,
			'image'    => Controls_Manager::MEDIA,
			'font'     => Controls_Manager::FONT,
			'dateTime' => Controls_Manager::DATE_TIME,
			'gallery'  => Controls_Manager::GALLERY,
			'array'    => Controls_Manager::REPEATER,
			'tabs'     => Controls_Manager::TABS,
			'tab'      => Controls_Manager::TAB,
			'section'  => Controls_Manager::TAB_CONTENT,
			'media'    => Controls_Manager::MEDIA,
			'url'      => Controls_Manager::URL,
		];

	public function handleFieldsAddControls($aFields, $hadTypeRepeater = false): array
	{
		$aData = [];
		if (empty($aFields)) {
			return [];
		}
		if ($hadTypeRepeater) {
			$oRepeater = new Repeater();
		}
		foreach ($aFields as $aItem) {
			if ($hadTypeRepeater) {
				$oRepeater->add_control(
					$aItem['id'],
					$aItem
				);
			} else {
				$aRawItem = $aItem;
				$aRawItem['type'] = $this->convertTypeElementor[$aItem['type']];
				$aData[] = $aRawItem;
			}
		}

		return $hadTypeRepeater ? $oRepeater->get_controls() : $aData;
	}

	public function handle(array $aData, Widget_Base $that)
	{
		//var_dump($aData);die();
		foreach ($aData as $aSession) {

			if ($aSession['type'] == 'section') {
				$aSessionField = $aSession;
				$aSessionField['tab'] = $this->convertTypeElementor[$aSession['type']];
				$that->start_controls_section(
					$aSession['id'],
					$aSessionField
				);

				if (!empty($aSession['fields'])) {
					foreach ($aSession['fields'] as $aFields) {

						if ($aFields['type'] == 'tabs') {
							$that->start_controls_tabs(
								$aFields['id']
							);

							if (!empty($aFields['fields'])) {

								foreach ($aFields['fields'] as $aItemFields) {

									if ($aItemFields['type'] == 'tab') {
										$that->start_controls_tab(
											$aItemFields['id'],
											[
												'label' => $aItemFields['label'],
											]
										);
										if (!empty($aItemFields['fields'])) {
											foreach ($aItemFields['fields'] as $aField) {
												$that->add_control(
													$aField['id'],
													$aField
												);
											}
										}
										$that->end_controls_tab();
									} else {
										$aFieldsControl = $aItemFields;
										$aFieldsControl['type'] = $this->convertTypeElementor[$aItemFields['type']];
										$aFieldsControl['fields']
											= $this->handleFieldsAddControls($aItemFields['fields'] ?? [],
											$aItemFields['type'] == 'array');
										$that->add_control(
											$aItemFields['id'],
											$aFieldsControl
										);
									}

								}

							}
							$that->end_controls_tabs();
						} elseif ($aFields['type'] == 'group') {
							if (!empty($aFields['fields'])) {
								$this->add_control(
									$aFields['id'],
									[
										'type'         => Controls_Manager::POPOVER_TOGGLE,
										'label'        => $aFields['label'],
										'label_off'    => 'Disable',
										'label_on'     => 'Enable',
										'return_value' => 'yes',
									]
								);
								$this->start_popover();
								foreach ($aFields['fields'] as $aFieldsItems) {
									$this->add_control(
										$aFieldsItems['id'],
										$aFieldsItems
									);
								}
								$this->end_popover();
							}
						} else {
							$aFieldsControl = $aFields;
							$aFieldsControl['type'] = $this->convertTypeElementor[$aFields['type']];
							$aFieldsControl['fields'] = $this->handleFieldsAddControls($aFields['fields'] ?? [],
								$aFieldsControl['type'] == 'array');
							$that->add_control(
								$aFieldsControl['id'],
								$aFieldsControl
							);
						}

					}
				}
				$that->end_controls_section();
			}
		}
	}
}