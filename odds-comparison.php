<?php
/*
Plugin Name: Odds Comparison
Plugin URI: https://www.oddsvalue.com
Description: Odds Comparison
Version: 1.12
Author: https://www.oddsvalue.com
Author URI: https://www.oddsvalue.com
*/



class Oddsvalue_Options {

	private static $instance = null;
	public $options;

	private $supported_language_arr = array(
		1	=> 'English',
		2	=> 'Spanish',
		3	=> 'Russian',
		4	=> 'Turkish',
		5	=> 'German',
		6	=> 'Portugese',
		7	=> 'Vietnamese',
		8	=> 'Macedonian',
		9	=> 'Serbian',
		10	=> 'Croatian',
		11	=> 'Bulgarian'
	);

	private $bookmaker_url_arr = array(
		6	=> 'Unibet',
		13	=> 'Sportingbet',
		17	=> 'Ladbrokes',
		26	=> 'Interwetten',
		30	=> 'Bwin',
		37	=> 'Fonbet',
		42	=> 'William Hill',
		49	=> 'Bet-at-home',
		75	=> 'Cash Point',
		83	=> 'Pinnacle',
		84	=> 'Paddy Power',
		118	=> 'Nordicbet',
		126	=> 'Bet365',
		221	=> 'Skybet',
		244	=> 'Coral',
		274	=> '10bet',
		291	=> 'Betfred',
		340	=> 'Betway',
		342	=> 'Betsson',
		363	=> 'Tipico',
		434	=> '12bet',
		532	=> 'Dafabet',
		539	=> 'Marathonbet',
		613	=> 'Betfair SB',
		622	=> 'Rivalo',
		631	=> 'NetBet',
		649	=> '1xbet',
	);

	private $supported_font_family_arr = array('Trebuchet MS', 'Verdana', 'Tahoma', 'Calibri', 'Sans Serif', 'Arial');

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	private function __construct() {

		// Add the page to the admin menu
		add_action( 'admin_menu', array( $this, 'add_page' ) );

		// Register page options
		add_action( 'admin_init', array( $this, 'register_page_options') );

		// Css rules for Color Picker
		//wp_enqueue_style( 'wp-color-picker' );

		// Register javascript
		add_action('wp_enqueue_scripts', array( $this, 'addScripts' ) );
		add_action('admin_enqueue_scripts', array( $this, 'addAdminScripts' ) );

		// Get registered option
		$this->options = get_option( 'oc_settings' );
		$this->options = is_array($this->options) ? $this->options : array();

	}

	function addAdminScripts() {
		wp_register_script(
			'odds_comparison_admin_color_picker',
			plugins_url( 'jquery.custom.js', __FILE__ ),
			array( 'jquery' )
		);

		wp_enqueue_script( 'odds_comparison_admin_color_picker' );
	}

	function addScripts() {

		wp_register_script(
			'odds_comparison_script',
			'https://oddsvalue.com/plugin/odds-comparison/creator.js',
			array( 'jquery' )
		);

		wp_enqueue_script( 'odds_comparison_script' );
	}


	/*--------------------------------------------*
	 * Functions
	 *--------------------------------------------*/

	/**
	 * Function that will add the options page under Setting Menu.
	 */
	public function add_page() {

		// $page_title, $menu_title, $capability, $menu_slug, $callback_function
		add_options_page( 'Theme Options', 'Odds Comparison', 'manage_options', __FILE__, array( $this, 'display_page' ) );


	}

	/**
	 * Function that will display the options page.
	 */
	public function display_page() {
		?>
        <div class="wrap">

            <h2>Odds Comparison Plugin Options</h2>
            <form method="post" action="options.php">
				<?php
				settings_fields(__FILE__);
				do_settings_sections(__FILE__);
				submit_button();
				?>
            </form>
            <h2>Notes</h2>
            <ul>
                <li>How to use the plugin?
                    <blockquote>
                        * Use [odds_comparison] shortcode where you want to display the odds comparison.
                    </blockquote>
                </li>
                <li>When you set "Author Credit Link" to "Off" means:
                    <blockquote>
                        * You are not linking back to author site.<br>
                        * Odds Comparison will work in an iframe that sized 100% x 5000px .<br>
                        * Color and Font customizations will be default setting.<br>
                    </blockquote>
                </li>
                <li>When you set "Author Credit Link" to "On" means:
                    <blockquote>
                        * You are linking back to author site.<br>
                        * Odds Comparison will work without an iframe.<br>
                        * Color and Font customizations will be your setting.<br>
                    </blockquote>
                </li>
            </ul>
        </div> <!-- /wrap -->
		<?php
	}
	/**
	 * Function that will register admin page options.
	 */
	public function register_page_options() {


		// Add Section for option fields
		add_settings_section( 'odds_comparison_main_section', 'Fonts, colors and other settings', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page

		add_settings_field( 'languageId', 'Language', array( $this, 'outputLanguageOptions' ), __FILE__, 'odds_comparison_main_section' ); // id, title, display cb, page, section
		add_settings_field( 'authorLink', 'Author Credit Link', array( $this, 'outputSettingAuthorLink' ), __FILE__, 'odds_comparison_main_section' ); // id, title, display cb, page, section
		add_settings_field( 'fontFamily', 'Font', array( $this, 'getFontFamilyOptions' ), __FILE__, 'odds_comparison_main_section' ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_font_color', 'Font Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_main_section', array('o_name' => 'colorFont', 'def_color' => $this->getOptionColor('colorFont')) ); // id, title, display cb, page, section

		/////////////////////////////////////////////////////////////////////
		// COLOR PICKER SETTINGS FOR MENU
		/////////////////////////////////////////////////////////////////////
		add_settings_section( 'odds_comparison_color_picker_section_menu', 'Menu colors', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page

		add_settings_field( 'color_picker_menu_top_bg', 'Menu Top Background Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuTopBg', 'def_color' => $this->getOptionColor('colorMenuTopBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_top_font', 'Menu Top Font Color', array( $this, 'getColorPicker'), __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuTopFont', 'def_color' => $this->getOptionColor('colorMenuTopFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_menu_sport_bg', 'Sport Background Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuSportBg', 'def_color' => $this->getOptionColor('colorMenuSportBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_sport_font', 'Sport Font Color', array( $this, 'getColorPicker'), __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuSportFont', 'def_color' => $this->getOptionColor('colorMenuSportFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_menu_region_bg', 'Region Background Color', array( $this, 'getColorPicker' )		, __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuRegionBg', 'def_color' => $this->getOptionColor('colorMenuRegionBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_region_font', 'Region Font Color', array( $this, 'getColorPicker' )		, __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuRegionFont', 'def_color' => $this->getOptionColor('colorMenuRegionFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_menu_competition_bg', 'Competition Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuCompetitionBg', 'def_color' => $this->getOptionColor('colorMenuCompetitionBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_competition_font', 'Competition Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_menu', array('o_name' => 'colorMenuCompetitionFont', 'def_color' => $this->getOptionColor('colorMenuCompetitionFont')) ); // id, title, display cb, page, section


		////////////////////////////////////////////////////////////////////
		// MAIN WINDOW NAVIGATION
		////////////////////////////////////////////////////////////////////
		add_settings_section( 'odds_comparison_color_picker_section_menu_right', 'Main Window Menu colors', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page
		add_settings_field( 'color_picker_menu_sport_list_bg', 'Main Window Navigation Sport Row Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuSportRowBg', 'def_color' => $this->getOptionColor('colorMainMenuSportRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_sport_list_font', 'Main Window Navigation Sport Row Font Color', array( $this, 'getColorPicker'), __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuSportRowFont', 'def_color' => $this->getOptionColor('colorMainMenuSportRowFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_menu_region_list_bg', 'Main Window Navigation Region Row Background Color', array( $this, 'getColorPicker' )		, __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuRegionRowBg', 'def_color' => $this->getOptionColor('colorMainMenuRegionRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_region_list_font', 'Main Window Navigation Region Row Font Color', array( $this, 'getColorPicker' )		, __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuRegionRowFont', 'def_color' => $this->getOptionColor('colorMainMenuRegionRowFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_menu_competition_list_bg', 'Main Window Navigation Competition Row Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuCompetitionRowBg', 'def_color' => $this->getOptionColor('colorMainMenuCompetitionRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_menu_competition_list_font', 'Main Window Navigation Competition Row Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_menu_right', array('o_name' => 'colorMainMenuCompetitionRowFont', 'def_color' => $this->getOptionColor('colorMainMenuCompetitionRowFont')) ); // id, title, display cb, page, section


		////////////////////////////////////////////////////////////////////
		// Data window options
		////////////////////////////////////////////////////////////////////
		add_settings_section( 'odds_comparison_color_picker_section_data_window', 'Odds Display colors', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page

		add_settings_field( 'color_picker_sport_row_bg', 'Sport Row Background Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorSportRowBg', 'def_color' => $this->getOptionColor('colorSportRowBg')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_competition_row_bg', 'Competition Row Background Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorCompetitionRowBg', 'def_color' => $this->getOptionColor('colorCompetitionRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_competition_row_font', 'Competition Row Font Color', array( $this, 'getColorPicker'), __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorCompetitionRowFont', 'def_color' => $this->getOptionColor('colorCompetitionRowFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_event_row_bg', 'Event Row Background Color', array( $this, 'getColorPicker' ), __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorEventRowBg', 'def_color' => $this->getOptionColor('colorEventRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_event_row_font', 'Event Row Font Color', array( $this, 'getColorPicker'), __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorEventRowFont', 'def_color' => $this->getOptionColor('colorEventRowFont')) ); // id, title, display cb, page, section


		add_settings_field( 'color_picker_button_market_type_bg', 'Market Type Button Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorMarketTypeButtonBg', 'def_color' => $this->getOptionColor('colorMarketTypeButtonBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_button_market_type_font', 'Market Type Button Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorMarketTypeButtonFont', 'def_color' => $this->getOptionColor('colorMarketTypeButtonFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_date_row_bg', 'Date Row Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorDateRowBg', 'def_color' => $this->getOptionColor('colorDateRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_date_row_font', 'Date Row Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorDateRowFont', 'def_color' => $this->getOptionColor('colorDateRowFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_market_type_row_bg', 'Market Type Header Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorMarketTypeRowBg', 'def_color' => $this->getOptionColor('colorMarketTypeRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_market_type_row_font', 'Market Type Header Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorMarketTypeRowFont', 'def_color' => $this->getOptionColor('colorMarketTypeRowFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_odds_row_bg', 'Odds Row Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorOddsRowBg', 'def_color' => $this->getOptionColor('colorOddsRowBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_odds_row_font', 'Odds Row Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_section_data_window', array('o_name' => 'colorOddsRowFont', 'def_color' => $this->getOptionColor('colorOddsRowFont')) ); // id, title, display cb, page, section

		add_settings_section( 'odds_comparison_color_picker_hover_colors', 'Hover colors', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page

		add_settings_field( 'color_picker_odds_row_hover_bg', 'Odds Row Hover Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorOddsRowHoverBg', 'def_color' => $this->getOptionColor('colorOddsRowHoverBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_odds_row_hover_font', 'Odds Row Hover Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorOddsRowHoverFont', 'def_color' => $this->getOptionColor('colorOddsRowHoverFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_button_market_type_hover_bg', 'Market Type Button Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorMarketTypeButtonHoverBg', 'def_color' => $this->getOptionColor('colorMarketTypeButtonHoverBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_button_market_type_hover_font', 'Market Type Button Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorMarketTypeButtonHoverFont', 'def_color' => $this->getOptionColor('colorMarketTypeButtonHoverFont')) ); // id, title, display cb, page, section

		add_settings_field( 'color_picker_button_market_type_hover_bg', 'Main Window Navigation Competition Row Background Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorMainMenuCompetitionRowHoverBg', 'def_color' => $this->getOptionColor('colorMainMenuCompetitionRowHoverBg')) ); // id, title, display cb, page, section
		add_settings_field( 'color_picker_button_market_type_hover_font', 'Main Window Navigation Competition Row Font Color', array( $this, 'getColorPicker' )	, __FILE__, 'odds_comparison_color_picker_hover_colors', array('o_name' => 'colorMainMenuCompetitionRowHoverFont', 'def_color' => $this->getOptionColor('colorMainMenuCompetitionRowHoverFont')) ); // id, title, display cb, page, section

		add_settings_section( 'odds_comparison_section_affiliate', 'Affiliate Links', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page

		$this->addBookmakerSettingFields();

		// Register Settings
		// register_setting( __FILE__, 'cpa_settings_options', array( $this, 'validateOptions' ) ); // option group, option name, sanitize cb
		register_setting( __FILE__, 'oc_settings', array( $this, 'validateOptions' ) ); // option group, option name, sanitize cb
	}

	private function addBookmakerSettingFields() {

		$bookmaker_url_arr = $this->bookmaker_url_arr;
		asort($bookmaker_url_arr);

		foreach ($bookmaker_url_arr as $bookmaker_id => $bookmaker_name) {
			$bookmaker_url = $this->getBookmakerUrl($bookmaker_id);

			add_settings_field( 'bookmakerUrl_'.$bookmaker_id, $bookmaker_name.' URL', function($bookmaker_params){
				echo '<input type="text" name="oc_settings[bookmakerUrl]['.$bookmaker_params[0].']" value="'.$bookmaker_params[1].'" />';
			}, __FILE__, 'odds_comparison_section_affiliate', array($bookmaker_id,$bookmaker_url) );
		}
	}

	private function getSetting($key) {
		return array_key_exists($key, $this->options) ? $this->options[$key] : null;
	}

	private function getBookmakerUrl($id) {
		if(is_null($bookmaker_url_arr = $this->getSetting('bookmakerUrl'))) {
			return '';
		} else if(!is_array($bookmaker_url_arr)) {
			return '';
		} else if(!array_key_exists($id, $bookmaker_url_arr)) {
			return '';
		}
		return $bookmaker_url_arr[$id];
	}

	private function getOptionColor($key) {
		if(is_array($this->options) && array_key_exists($key, $this->options)) {
			$color = $this->options[$key];
		} else {
			switch ($key) {
				case 'colorFont':					$color = '#505050'; break;

				case 'colorMenuTopBg':				$color = '#579259'; break;
				case 'colorMenuTopFont':			$color = '#FFFFFF'; break;
				case 'colorMenuSportBg':			$color = '#444444'; break;
				case 'colorMenuSportFont':			$color = '#FFFFFF'; break;
				case 'colorMenuRegionBg':			$color = '#666666'; break;
				case 'colorMenuRegionFont':			$color = '#FFFFFF'; break;
				case 'colorMenuCompetitionBg':		$color = '#888888'; break;
				case 'colorMenuCompetitionFont':	$color = '#FFFFFF'; break;

				case 'colorCompetitionRowBg':		$color = '#3B6170'; break;
				case 'colorCompetitionRowFont':		$color = '#FFFFFF'; break;
				case 'colorEventRowBg':				$color = '#597A87'; break;
				case 'colorEventRowFont':			$color = '#FFFFFF'; break;
				case 'colorMarketTypeButtonBg':		$color = '#579259'; break;
				case 'colorMarketTypeButtonFont':	$color = '#FFFFFF'; break;
				case 'colorDateRowBg':				$color = '#DDDDDD'; break;
				case 'colorDateRowFont':			$color = '#505050'; break;
				case 'colorMarketTypeRowBg':		$color = '#597a87'; break;
				case 'colorMarketTypeRowFont':		$color = '#FFFFFF'; break;
				case 'colorOddsRowBg':				$color = '#FAFAFA'; break;
				case 'colorOddsRowFont':			$color = '#505050'; break;

				case 'colorOddsRowHoverBg':			$color = '#9cd69e'; break;
				case 'colorOddsRowHoverFont':		$color = '#505050'; break;

				case 'colorMarketTypeButtonHoverBg':		$color = '#9cd69e'; break;
				case 'colorMarketTypeButtonHoverFont':		$color = '#505050'; break;

				// MAIN NAVIGATION
				case 'colorMainMenuSportRowBg':					$color = '#3B6170'; break;
				case 'colorMainMenuSportRowFont':				$color = '#FFFFFF'; break;
				case 'colorMainMenuRegionRowBg':				$color = '#597A87'; break;
				case 'colorMainMenuRegionRowFont':				$color = '#FFFFFF'; break;
				case 'colorMainMenuCompetitionRowBg':			$color = '#FFFFFF'; break;
				case 'colorMainMenuCompetitionRowFont':			$color = '#000000'; break;
				case 'colorMainMenuCompetitionRowHoverBg':		$color = '#000000'; break;
				case 'colorMainMenuCompetitionRowHoverFont':	$color = '#FFFFFF'; break;

				default:
					$color = '#000000';
			}
		}



		return $color;
	}

	/**
	 * Function that will add javascript file for Color Piker.
	 */
	public function enqueue_admin_js() {

		// Make sure to add the wp-color-picker dependecy to js file
		wp_enqueue_script( 'cpa_custom_js', plugins_url( 'jquery.custom.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
	}
	/**
	 * Function that will validate all fields.
	 */
	public function validateOptions( $fields ) {

		$valid_fields = array();

		foreach ($fields as $key => $value) {

			if(!is_array($value)) {
				$value = trim($value);
			}

			$fields[$key] = $value;

			if(substr($key, 0, 5) == 'color') {
				if(!$this->validateColor($value)) {
					add_settings_error('oc_setting', 'oc_error', 'Insert a valid color', 'error' );

					if(array_key_exists($key, $this->options)) {
						$valid_fields[$key] = $this->options[$key];
					} else {
						unset($fields[$key]);
					}

				} else {
					$valid_fields[$key] = $value;
				}
			} else if($key == 'languageId') {
				if(array_key_exists($value, $this->supported_language_arr)) {
					$valid_fields[$key] = $value;
				} else {
					add_settings_error('oc_setting', 'oc_error', 'Invalid language selected', 'error' );
					$valid_fields[$key] = 1;
				}
			} else if($key == 'fontFamily') {
				if(in_array($value, $this->supported_font_family_arr)) {
					$valid_fields[$key] = $value;
				} else {
					add_settings_error('oc_setting', 'oc_error', 'Invalid font selected', 'error' );
					$valid_fields[$key] = 'Verdana';
				}
			} else if($key == 'authorLink') {
				if(!in_array($value, array(0, 1))) {
					$valid_fields[$key] = 0;
				} else {
					$valid_fields[$key] = $value;
				}
			} else if($key == 'bookmakerUrl') {

				foreach ($value as $bookmaker_id => $bookmaker_url) {
					$bookmaker_url = trim($bookmaker_url);
					if(strpos($bookmaker_url, 'http://') === 0 || strpos($bookmaker_url, 'https://') === 0) {
						$valid_fields[$key][$bookmaker_id] = $bookmaker_url;
					}
				}
			}
		}

		return apply_filters( 'validateOptions', $valid_fields, $fields);

	}
	/**
	 * Function that will check if value is a valid HEX color.
	 */
	public function validateColor($value) {

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #
			return true;
		}

		return false;
	}
	/**
	 * Callback function for settings section
	 */
	public function display_section() { /* Leave blank */ }

	/**
	 * Functions that display the fields.
	 */
	public function title_settings_field() {

		$val = ( isset( $this->options['title'] ) ) ? $this->options['title'] : '';
		echo '<input type="text" name="cpa_settings_options[title]" value="' . $val . '" />';
	}
	public function bz_your_word_cb() {
		echo '<input type="hidden" name="cpa_settings_options[bz_your_word]" value="" />';
	}
	public function outputSettingAuthorLink() {

		$selected_key = (is_array($this->options) && array_key_exists('authorLink', $this->options)) ? $this->options['authorLink'] : 0;

		$value_arr = array(
			0	=> 'Off',
			1	=> 'On'
		);

		echo	'<select name="oc_settings[authorLink]">';

		foreach ($value_arr as $key => $value) {
			echo	'<option value="'.$key.'" '.($key == $selected_key ? 'selected' : '').'>'.$value.'</option>';
		}

		echo	'</select>';
	}
	public function getFontFamilyOptions() {

		$selected_value = (is_array($this->options) && array_key_exists('fontFamily', $this->options)) ? $this->options['fontFamily'] : 0;

		echo	'<select name="oc_settings[fontFamily]">';

		foreach ($this->supported_font_family_arr as $value) {
			echo	'<option value="'.$value.'" '.($value == $selected_value ? 'selected' : '').'>'.$value.'</option>';
		}

		echo	'</select>';
	}

	public function outputLanguageOptions() {

		$language_id = (is_array($this->options) && array_key_exists('languageId', $this->options)) ? $this->options['languageId'] : 0;

		echo	'<select name="oc_settings[languageId]">';

		foreach ($this->supported_language_arr as $id => $name) {
			echo	'<option value="'.$id.'" '.($id == $language_id ? 'selected' : '').'>'.$name.'</option>';
		}

		echo	'</select>';
	}

	public function getColorPicker(array $args) {

		if(array_key_exists($args['o_name'], $this->options)) {
			$color = $this->options[$args['o_name']];
		} else {
			$color = $args['def_color'];
		}

		echo '<input type="text" name="oc_settings['.$args['o_name'].']" value="'.$color.'" class="cpa-color-picker" >';
	}


} // end class

Oddsvalue_Options::get_instance();

function get_odds_comparison($atts) {

	$obj_arr = array();

	$settings = get_option('oc_settings');

	if(array_key_exists('authorLink', $settings) && $settings['authorLink'] == 1) {

		$html	=	'<script type="text/javascript">'
			//.		'OddsComparison.open({'.implode(', ', $obj_arr).'});'
			.		'OddsComparison.open('.json_encode($settings).');'
			.	'</script>'
			.	'<div id="OddsValue"><a href="https://www.oddsvalue.com">Odds Comparison</a></div>';
	} else {
		$language_id = array_key_exists('languageId', $settings) ? $settings['languageId'] : 1;

		$html	=	'<iframe src="https://www.oddsvalue.com/plugin/odds-comparison?language_id='.$language_id.'" frameborder="0" style="width:100%;height:5000px;"></iframe>';
	}


	return    $html;
}

add_shortcode( 'odds_comparison', 'get_odds_comparison' );

?>