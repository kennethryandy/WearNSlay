<?php


/**
 * Class that wraps the core theme functionality.
 */
class WearNSlay
{
	/**
	 * The theme version, as used by sricpts, etc.
	 * @var string
	 */
	protected $themeVersion;


	public function __construct($themeVersion)
	{
		$this->themeVersion  =  $themeVersion;

		// Set up initialisation hooks
		add_action('init', 					[$this, 	'init']);

		// ACF initialisations
		$this->init_ACF();
	}


	/**
	 * Core init called by WordPress on init().
	 */
	public function init()
	{
		// Menu Related
		// $this->initMenus();

		// Actions
		// Load scripts and styles
		add_action('wp_enqueue_scripts', 			[$this, 'init_theme_scriptsAndStyles']);
	}

	/**
	 * Adds the styles and scripts to the page.
	 */
	public function init_theme_scriptsAndStyles()
	{
		$path  =  self::getAssetsPath('');

		wp_enqueue_style('wear-n-slay', $path . 'css/wear-n-slay.min.css', ['botiga-style', 'botiga-style-min', 'botiga-custom-styles'], filemtime(get_stylesheet_directory() . '/assets/css/wear-n-slay.min.css'), 'all');

		// Scripts
		wp_enqueue_script('wear-n-slay', $path . 'js/wear-n-slay.min.js',  ['jquery'],  filemtime(get_stylesheet_directory() . '/assets/js/wear-n-slay.min.js'), true);
	}


	/**
	 * Initalise menu settings.
	 */
	protected function initMenus()
	{
		register_nav_menu('footer-menu', __('Footer Menu'));
		register_nav_menu('footer-menu-terms', __('Footer Menu - Terms'));
	}


	/**
	 * Functions for setting up ACF.
	 */
	public function init_ACF()
	{
		// Create a settings page.
		if (function_exists('acf_add_options_page'))
		{
			$menuSlug  =  'website-settings';

			acf_add_options_page(array(
				'page_title'  => __('Hi! Virtual - Website Settings'),
				'menu_title'  => __('Website Settings'),
				'menu_slug'   => $menuSlug,
				'redirect'    => false
			));
		}
	}


	/**
	 * Method that handles rendering the page content using the layout blocks.
	 * @param integer $pageID The ID of the page being rendered.
	 */
	public static function handleBlockLayout($pageID)
	{
		if (have_rows('layout_blocks', $pageID))
		{
			while (have_rows('layout_blocks', $pageID))
			{
				the_row();
				$layout = get_row_layout();

				// Convert the name of the layout to the file format. The structure is:
				// block__video_welcome, so this needs to become video-welcome
				$blockName = false;
				if (preg_match('%^block__([\_a-zA-Z0-9]+)$%', $layout, $matches))
				{
					$blockName = str_replace('_', '-', $matches[1]);
				}

				// See if the template file exists? All templates should be in template-blocks. 
				$templateFile = 'template-block/' . $blockName . '.php';
				if (!empty($blockName) && !empty($blockTFile = locate_template($templateFile)))
				{
					require($blockTFile);
				}

				else
				{
					printf('<div class="container"><b>%s</b> (<code>%s</code>)</div>', __('Template not found.'), esc_html($templateFile));
				}
			}
		}
	}



	/**
	 * Get the assets path for this theme.
	 *
	 * @param string $subPath If provided, add this to the URL that's returned.
	 * @return string The path to the assets (including the subpath if included).
	 */
	public static function getAssetsPath($subPath = false)
	{
		return get_stylesheet_directory_uri() . '/assets/' . $subPath;
	}


	/**
	 * Returns the URL of the image folder for images used in the theme.
	 * 
	 * @param string $filename If specified, append this filename to the URL for the image folder.
	 * @return string The URL to the image path
	 */
	public static function images_getImageURL($filename = false)
	{
		return get_stylesheet_directory_uri() . '/assets/img/' . $filename;
	}


	/**
	 * Static factory method to initialise this theme and return an instantiated
	 * object.
	 *
	 * @param string $themeVersion The current theme version.
	 *
	 * @return WearNSlay The instantiated theme object.
	 */
	public static function createTheme($themeVersion)
	{
		$theme = new WearNSlay($themeVersion);
		return $theme;
	}


	/**
	 * Debug function - shows an array on the screen, and in the PHP error log for debug purposes.
	 * @param array $data The array of data to show and display.
	 * @param string $label If specified, show this as a label for the data that's been shown to make it clearer what the data applies to.
	 * @param bool $errorlogOnly If true, do not echo the data to the screen, error log only.
	 */
	public static function __debug_showArray($data, $label = false, $errorLogOnly = false)
	{
		// Break out a preformatted array
		$debugData = print_r($data, true);

		// Add a label prefix if there is one
		if ($label)
		{
			$debugData = $label . ":\n" . $debugData;
		}

		// Show to the screen if available.
		if (!$errorLogOnly)
		{
			printf('<div><pre style="padding: 20px; background: #efefef; border: 2px solid #ddd; margin: 20px;">%s</pre></div>', htmlentities($debugData));
		}

		// Show in the PHP error log
		error_log($debugData);
	}
}
