<?php
namespace yamarket_fusion\Base;

class ClassMap {
	public static $models = array(
		/*
		'admin' => array(
			'event' => '\\yamarket_fusion\\Module\\models\\Event',
			'setting' => '\\yamarket_fusion\\Module\\models\\Setting',
			'catalog' => '\\yamarket_fusion\\Module\\models\\admin\\Catalog',
			'module' => '\\yamarket_fusion\\Module\\models\\Module',
			'addon/related_options' => '\\yamarket_fusion\\Module\\models\\addon\\related_options\\RelatedOptions',
			'addon/currency_plus' => '\\yamarket_fusion\\Module\\models\\addon\\currency_plus\\CurrencyPlus',
			'addon/group_price' => '\\yamarket_fusion\\Module\\models\\addon\\group_price\\GroupPrice',
		),
		'catalog' => array(
			'catalog' => '\\yamarket_fusion\\Module\\models\\catalog\\Catalog',
			'setting' => '\\yamarket_fusion\\Module\\models\\Setting',
			'module' => '\\yamarket_fusion\\Module\\models\\Module',
			'addon/related_options' => '\\yamarket_fusion\\Module\\models\\addon\\related_options\\RelatedOptions',
			'addon/currency_plus' => '\\yamarket_fusion\\Module\\models\\addon\\currency_plus\\CurrencyPlus',
			'addon/group_price' => '\\yamarket_fusion\\Module\\models\\addon\\group_price\\GroupPrice',
		),
		*/
		'aliases' => array(
			'setting'					=> '\\yamarket_fusion\\Module\\models\\Setting',
			'event'						=> '\\yamarket_fusion\\Module\\models\\Event',
			'module'					=> '\\yamarket_fusion\\Module\\models\\Module',
			'admin/catalog'				=> '\\yamarket_fusion\\Module\\models\\admin\\Catalog',
			'image'						=> '\\yamarket_fusion\\Module\\models\\Image',
			'catalog/catalog'			=> '\\yamarket_fusion\\Module\\models\\catalog\\Catalog',
			'addon/related_options'		=> '\\yamarket_fusion\\Module\\models\\addon\\related_options\\RelatedOptions',
			'addon/currency_plus'		=> '\\yamarket_fusion\\Module\\models\\addon\\currency_plus\\CurrencyPlus',
			'addon/group_price'			=> '\\yamarket_fusion\\Module\\models\\addon\\group_price\\GroupPrice',
			'addon/image_option_change'	=> '\\yamarket_fusion\\Module\\models\\addon\\image_option_change\\ImageOptionChange',
			'addon/podarki'				=> '\\yamarket_fusion\\Module\\models\\addon\\podarki\\Podarki',
			'addon/multistore'			=> '\\yamarket_fusion\\Module\\models\\addon\\multistore\\Multistore',
		),
	);
}